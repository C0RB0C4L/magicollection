<?php

namespace App\Controller;

use App\Form\RegistrationForm;
use App\Repository\AccountCreationRequestRepository;
use App\Security\UserSecurityManagerInterface;
use App\Service\FlashMessageService;
use App\Service\MailerServiceInterface;
use App\Service\User\RegistrationFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register', name: 'app_registration_')]
class RegistrationController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Request $request, RegistrationFactoryInterface $factory, MailerServiceInterface $mailer): Response
    {
        $form = $this->createForm(RegistrationForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userToCreate = $form->getData();
            $accountRequest = $factory->createAccountRequest($userToCreate);

            if ($accountRequest !== null) {

                $mailer->sendAccountCreationRequestEmail($accountRequest);

                return $this->redirectToRoute("app_registration_success", [
                    "userId" => $accountRequest->getSelector()
                ]);
            } else {

                $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
            }
        }

        return $this->render('registration/index.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/success', name: 'success')]
    public function success(Request $request, AccountCreationRequestRepository $accountCreationRepo): Response
    {
        // is sent as GET parameter from the registration index on success
        $requestParameter = $request->get("userId");

        $accountRequest = $accountCreationRepo->findOneBy(["selector" => $requestParameter]);

        if ($accountRequest !== null) {

            return $this->render("registration/success.html.twig", []);
        } else {

            $this->addFlash(FlashMessageService::MSG_ERROR, FlashMessageService::MSG_ERROR);

            return $this->redirectToRoute("app_registration_index");
        }
    }


    #[Route('/check', name: 'check')]
    public function check(
        Request $request,
        AccountCreationRequestRepository $accountCreationRepo,
        UserSecurityManagerInterface $security,
        MailerServiceInterface $mailer
    ): Response {

        // registration ID is the contatenation of "<selector>.<token>"
        $requestParameter = $request->get("registrationId");

        if ($requestParameter) {

            $separatorIndex = strpos($requestParameter, ".");
            $selector = substr($requestParameter, 0, $separatorIndex);
            $token = substr($requestParameter, $separatorIndex + 1, strlen($requestParameter));

            $accountRequest = $accountCreationRepo->findOneBy(['selector' => $selector, 'token' => $token, "confirmedAt" => null]);

            if ($accountRequest) {

                $security->verify($accountRequest);
                $mailer->sendAccountCreationRequestConfirmed($accountRequest);

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
            }
        } else {

            return $this->redirectToRoute("app_home");
        }

        return $this->redirectToRoute("app_login");
    }
}
