<?php

namespace App\Controller;

use App\Repository\AccountCreationRequestRepository;
use App\Service\FlashMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_main_')]
class MainController extends AbstractController
{
    #[Route('', name: 'home')]
    public function home(AccountCreationRequestRepository $re): Response
    {
        return $this->render('main/home.html.twig', []);
    }
}
