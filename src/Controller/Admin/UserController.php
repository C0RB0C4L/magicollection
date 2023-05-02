<?php

namespace App\Controller\Admin;

use App\Form\UserForm;
use App\Repository\UserRepository;
use App\Security\UserSecurityManager;
use App\Security\UserSecurityManagerInterface;
use App\Service\FlashMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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


    #[Route('/{id}/edit', name: 'edit')]
    public function edit(int $id, Request $request, UserRepository $userRepo): Response
    {
        $user = $userRepo->find($id);

        if ($user !== null) {

            $form = $this->createForm(UserForm::class, $user);
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

        if ($security->isGranted($user, UserSecurityManager::ADMIN)) {

            $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::MSG_WARNING);

            return $this->redirectToRoute("app_admin_user_index");
        }

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

        if ($security->isGranted($user, UserSecurityManager::ADMIN)) {

            $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::MSG_WARNING);

            return $this->redirectToRoute("app_admin_user_index");
        }

        if ($user !== null) {

            $security->deactivate($user);

            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_user_index");
    }
}
