<?php

namespace App\Controller;

use App\Form\EmailEditForm;
use App\Form\PasswordEditForm;
use App\Security\UserSecurityManagerInterface;
use App\Service\FlashMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account', name: 'app_account_')]
class AccountController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', []);
    }


    #[Route('/edit-password', name: 'password_edit_ajax')]
    public function passwordEdit(Request $request, UserSecurityManagerInterface $security): Response
    {
        $form = $this->createForm(PasswordEditForm::class);

        if ($request->isXmlHttpRequest() && $this->getUser()) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $plainPassword = $form->get("password")->getData();

                $security->updatePassword($this->getUser(), $plainPassword);

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

                return new JsonResponse(["status" => 1, "url" => $this->generateUrl("app_account_index")]);
            }

            $responseBody = $this->renderForm('account/modals/password_edit.html.twig', [
                'form' => $form
            ]);

            return new JsonResponse(["status" => 0, "body" => $responseBody->getContent()]);
        } else {

            return $this->render('account/modals/password_edit.html.twig', [
                "form" => $form->createView()
            ]);
        }
    }


    #[Route('/edit-email', name: 'email_edit_ajax')]
    public function emailEdit(Request $request, UserSecurityManagerInterface $security): Response
    {
        $form = $this->createForm(EmailEditForm::class);

        if ($request->isXmlHttpRequest() && $this->getUser()) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $email = $form->get("email")->getData();

                $security->updateEmail($this->getUser(), $email);

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

                return new JsonResponse(["status" => 1, "url" => $this->generateUrl("app_account_index")]);
            }

            $responseBody = $this->renderForm('account/modals/email_edit.html.twig', [
                'form' => $form
            ]);

            return new JsonResponse(["status" => 0, "body" => $responseBody->getContent()]);
        } else {

            return $this->render('account/modals/email_edit.html.twig', [
                "form" => $form->createView()
            ]);
        }
    }
}
