<?php

namespace App\Controller\Admin;

use App\Form\RegistrationForm;
use App\Form\UserEditForm;
use App\Form\UserRolesEditForm;
use App\Repository\UserRepository;
use App\Security\UserSecurityManager;
use App\Security\UserSecurityManagerInterface;
use App\Service\MailerServiceInterface;
use App\Service\FlashMessageService;
use App\Service\User\UserFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user', name: 'app_admin_user_')]
class UserController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(UserRepository $userRepo): Response
    {
        if ($this->isGranted(UserSecurityManager::MASTER)) {

            $users = $userRepo->findAll();
        } else {
            $users = $userRepo->findAllButMaster();
        }

        return $this->render('admin/user/index.html.twig', [
            "users" => $users
        ]);
    }


    #[Route('/create', name: 'create')]
    public function create(Request $request, UserFactoryInterface $userFactory): Response
    {
        $form = $this->createForm(RegistrationForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userToCreate = $form->getData();
            // bypasses the accountCreationRequest entity process and is already verified and active
            $createdUser = $userFactory->createUser($userToCreate, [UserSecurityManager::BASIC], true, true);

            if ($createdUser !== null) {

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

                return $this->redirectToRoute("app_admin_user_edit", ["id" => $createdUser->getId()]);
            } else {
                $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
            }
        }

        return $this->render('admin/user/create.html.twig', [
            "form" => $form->createView()
        ]);
    }


    #[Route('/{id}/edit', name: 'edit')]
    public function edit(int $id, Request $request, UserRepository $userRepo, UserSecurityManagerInterface $security): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null) {

            if ($security->protectSelfAndMaster($user, $this->getUser())) {
                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::TYPE_WARNING);

                return $this->redirectToRoute("app_admin_user_index");
            }

            $form = $this->createForm(UserEditForm::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $user = $form->getData();
                $userRepo->save($user, true);

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
            }

            return $this->render('admin/user/edit.html.twig', [
                "editForm" => $form->createView()
            ]);
        }

        $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);

        return $this->redirectToRoute("app_admin_user_index");
    }


    #[Route('/{id}/roles', name: 'roles')]
    public function roles(int $id, Request $request, UserRepository $userRepo, UserSecurityManagerInterface $security, RequestStack $stack): Response
    {
        // prevents direct access
        if ($stack->getParentRequest() === null && $request->getMethod() !== "POST") {

            return $this->redirectToRoute("app_admin_user_edit", ["id" => $id]);
        }

        $user = $userRepo->find($id);

        if ($security->protectSelfAndMaster($user, $this->getUser())) {
            $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::TYPE_WARNING);

            return $this->redirectToRoute("app_admin_user_index");
        }

        if ($user !== null) {
            
            $form = $this->createForm(UserRolesEditForm::class, $user);
            $security->isMaster($user) ? $isMaster = true : $isMaster = false;
            $form->handleRequest($request);
            dump($isMaster);

            if ($form->isSubmitted() && $form->isValid()) {

                $roles = $form->get("roles")->getData();
                $security->updateRoles($user, $roles, $isMaster, true);

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

                return $this->redirectToRoute("app_admin_user_edit", ["id" => $user->getId()]);
            }

            return $this->render('admin/user/_partials/edit_roles_form.html.twig', [
                "form" => $form->createView()
            ]);
        }
    }


    #[Route('/{id}/activate', name: 'activate')]
    public function activate(int $id, UserRepository $userRepo, UserSecurityManagerInterface $security): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null) {

            $security->activate($user);

            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_user_index");
    }


    #[Route('/{id}/deactivate', name: 'deactivate')]
    public function deactivate(int $id, UserRepository $userRepo, UserSecurityManagerInterface $security): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null) {

            if ($security->protectAll($user, $this->getUser())) {
                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::TYPE_WARNING);

                return $this->redirectToRoute("app_admin_user_index");
            }

            $security->deactivate($user);

            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_user_index");
    }


    #[Route('/{id}/reset-password', name: 'reset_password')]
    public function resetPassword(int $id, UserRepository $userRepo, UserSecurityManagerInterface $security, MailerServiceInterface $mailer): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null) {

            if ($security->protectAll($user, $this->getUser())) {
                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::TYPE_WARNING);

                return $this->redirectToRoute("app_admin_user_index");
            }

            $newPassword = $security->regeneratePassword($user);
            $email = $mailer->sendManualPasswordResetEmail($user, $newPassword);

            if ($email) {
                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
            } else {
                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::TYPE_WARNING);
            }
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_user_edit", ["id" => $user->getId()]);
    }


    #[Route('/{id}/delete', name: 'delete')]
    public function delete(int $id, UserRepository $userRepo, UserSecurityManagerInterface $security): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null) {

            if ($security->protectAll($user, $this->getUser())) {
                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::TYPE_WARNING);

                return $this->redirectToRoute("app_admin_user_index");
            }

            $security->delete($user);

            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_user_index");
    }


    #[Route('/{id}/verify', name: 'verify')]
    public function verify(int $id, UserRepository $userRepo, UserSecurityManagerInterface $security, MailerServiceInterface $mailer): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null && $user->getAccountCreationRequest() !== null) {

            $accountCreationRequest = $user->getAccountCreationRequest();
            $security->verify($accountCreationRequest);
            $mailer->sendAccountCreationRequestConfirmed($accountCreationRequest);
            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

            return $this->redirectToRoute("app_admin_user_edit", ["id" => $user->getId()]);
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);

            return $this->redirectToRoute("app_admin_user_edit", ["id" => $user->getId()]);
        }

        return $this->redirectToRoute("app_admin_user_index");
    }


    #[Route('/{id}/resend-verification-email', name: 'resend_email')]
    public function resendVerification(int $id, UserRepository $userRepo, MailerServiceInterface $mailer): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null && $user->getAccountCreationRequest() !== null) {

            $accountCreationRequest = $user->getAccountCreationRequest();
            $mailer->sendAccountCreationRequestEmail($accountCreationRequest);

            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
        } else {
            $this->addFlash(FlashMessageService::MSG_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_user_edit", ["id" => $id]);
    }
}
