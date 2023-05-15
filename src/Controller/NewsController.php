<?php

namespace App\Controller;

use App\Repository\NewsRepository;
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

        return $this->render('news/index.html.twig', []);
    }

    #[Route('/{id}', name: 'detail')]
    public function detail(int $id, NewsRepository $newsRepo): Response
    {
        $news = $newsRepo->find($id);

        if ($news !== null) {
            dump('works but there\'s work to do');
            die;
        }

        return $this->redirectToRoute('app_news_index');
    }
}
