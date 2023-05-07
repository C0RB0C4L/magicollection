<?php

namespace App\Controller;

use App\Form\UserCreateForm;
use App\Repository\AccountCreationRequestRepository;
use App\Security\UserSecurityManagerInterface;
use App\Service\FlashMessageService;
use App\Service\User\UserFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register', name: 'app_registration_')]
class RegistrationController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Request $request, UserFactoryInterface $userFactory): Response
    {
        $form = $this->createForm(UserCreateForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $username = $form->get('username')->getData();
            $plainPassword = $form->get('password')->getData();
            $email = $form->get('email')->getData();

            $user = $userFactory->createSimpleUser($username, $email, $plainPassword, false, false);
            //$user = $form->getData();

            if ($user !== null) {
                $request = $userFactory->accountCreationRequest($user);

                return $this->render("registration/success.html.twig", []);
            } else {

                $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
            }
        }

        return $this->render('registration/index.html.twig', [
            "form" => $form->createView()
        ]);
    }


    #[Route('/check', name: 'check')]
    public function check(Request $request, AccountCreationRequestRepository $accountCreationRepo, UserSecurityManagerInterface $security): Response
    {
        $requestParameter = $request->get("registrationId");

        if ($requestParameter) {

            $separatorIndex = strpos($requestParameter, ".");
            $selector = substr($requestParameter, 0, $separatorIndex);
            $token = substr($requestParameter, $separatorIndex + 1, strlen($requestParameter));

            $accountRequest = $accountCreationRepo->findOneBy(['selector' => $selector, 'token' => $token, "confirmedAt" => null]);

            if ($accountRequest) {

                $security->verify($accountRequest->getUser(), $accountRequest->getId());

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
            }
        } else {

            return $this->redirectToRoute("app_main_home");
        }

        return $this->redirectToRoute("app_login");
    }
}
