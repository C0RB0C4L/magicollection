<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use App\Service\FlashMessageService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/news', name: 'app_news_')]
class NewsController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(NewsRepository $newsRepo): Response
    {
        $news = $newsRepo->findAll();

        return $this->render('news/index.html.twig', [
            "news" => $news
        ]);
    }

    #[Route('/{date}/{slug}', name: 'detail')]
    public function detail(string $date, string $slug, NewsRepository $newsRepo): Response
    {
        $date = DateTimeImmutable::createFromFormat("Y-m-d", $date);
        $date = $date->setTime(0, 0, 0);

        $news = $newsRepo->findWithSlugAndDate($date, $slug);

        if ($news !== null) {

            return $this->render('news/detail.html.twig', [
                "news" => $news
            ]);
        }

        $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);

        return $this->redirectToRoute('app_news_index');
    }
}
