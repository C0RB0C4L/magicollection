<?php

namespace App\Controller;

use App\Service\HomeDataServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_')]
class HomeController extends AbstractController
{
    #[Route('', name: 'home')]
    public function home(HomeDataServiceInterface $homeData): Response
    {
        $lastNews = $homeData->getLastNews();
     
        return $this->render('home/home.html.twig', [
            "news" => $lastNews
        ]);
    }
}
