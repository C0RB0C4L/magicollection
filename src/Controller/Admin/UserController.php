<?php

namespace App\Controller\Admin;

use App\Form\UserCreateForm;
use App\Form\UserEditForm;
use App\Repository\UserRepository;
use App\Security\UserSecurityManagerInterface;
use App\Service\Email\MailerServiceInterface;
use App\Service\FlashMessageService;
use App\Service\User\UserFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user', name: 'app_admin_user_')]
class UserController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('admin/user/index.html.twig', [
            "users" => $users
        ]);
    }


    #[Route('/create', name: 'create')]
    public function create(Request $request, UserFactoryInterface $userFactory): Response
    {
        $form = $this->createForm(UserCreateForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $username = $form->get('username')->getData();
            $plainPassword = $form->get('password')->getData();
            $email = $form->get('email')->getData();

            $user = $userFactory->createSimpleUser($username, $email, $plainPassword, true, true);

            if ($user !== null) {

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

                return $this->redirectToRoute("app_admin_user_edit", ["id" => $user->getId()]);
            }
        }

        return $this->render('admin/user/create.html.twig', [
            "form" => $form->createView()
        ]);
    }


    #[Route('/{id}/edit', name: 'edit')]
    public function edit(int $id, Request $request, UserRepository $userRepo): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null) {

            $form = $this->createForm(UserEditForm::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $userRepo->save($user, true);

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
            }

            return $this->render('admin/user/edit.html.twig', [
                "form" => $form->createView()
            ]);
        }

        $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);

        return $this->redirectToRoute("app_admin_user_index");
    }


    #[Route('/{id}/activate', name: 'activate')]
    public function activate(int $id, UserRepository $userRepo, UserSecurityManagerInterface $security): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null) {

            if (
                $security->protectBrothersAndMaster($user, $this->getUser())
                || $security->preventSelfHarm($user, $this->getUser())
            ) {
                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::MSG_WARNING);

                return $this->redirectToRoute("app_admin_user_index");
            }

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

            if (
                $security->protectBrothersAndMaster($user, $this->getUser())
                || $security->preventSelfHarm($user, $this->getUser())
            ) {

                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::MSG_WARNING);

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

            if ($security->preventSelfHarm($user, $this->getUser())) {

                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::MSG_WARNING);

                return $this->redirectToRoute("app_admin_user_edit", ["id" => $user->getId()]);
            }

            $newPassword = $security->generatePassword();
            $security->updatePassword($user, $newPassword);
            $email = $mailer->sendManualResetPassword($user, $newPassword);

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

            if (
                $security->protectBrothersAndMaster($user, $this->getUser())
                || $security->preventSelfHarm($user, $this->getUser())
            ) {

                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::MSG_WARNING);

                return $this->redirectToRoute("app_admin_user_edit", ["id" => $user->getId()]);
            }

            $userRepo->remove($user, true);

            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_user_index");
    }


    #[Route('/{id}/verify', name: 'verify')]
    public function verify(int $id, UserRepository $userRepo, UserSecurityManagerInterface $security): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null && $user->getAccountCreationRequest() !== null) {

            $accountCreationId = $user->getAccountCreationRequest()->getId();
            $security->verify($user, $accountCreationId);

            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

            return $this->redirectToRoute("app_admin_user_edit", ["id" => $user->getId()]);
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);

            return $this->redirectToRoute("app_admin_user_edit", ["id" => $user->getId()]);
        }

        return $this->redirectToRoute("app_admin_user_index");
    }


    #[Route('/{id}/resend-email', name: 'resend_email')]
    public function resendVerification(int $id, UserRepository $userRepo, UserSecurityManagerInterface $security): Response
    {
        $this->addFlash(FlashMessageService::TYPE_NOTICE, FlashMessageService::MSG_INFO);

        return $this->redirectToRoute("app_admin_user_edit", ["id" => $id]);
    }
}
