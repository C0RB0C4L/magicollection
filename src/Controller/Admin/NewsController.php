<?php

namespace App\Controller\Admin;

use App\Form\NewsForm;
use App\Repository\NewsRepository;
use App\Service\FlashMessageService;
use App\Service\News\NewsFactoryInterface;
use App\Service\News\NewsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/news', name: 'app_admin_news_')]
class NewsController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(NewsRepository $newsRepo): Response
    {
        $news = $newsRepo->findAll();

        return $this->render('admin/news/index.html.twig', [
            "news" => $news
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, NewsFactoryInterface $newsFactory): Response
    {
        $form = $this->createForm(NewsForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $news = $form->getData();
            $newsFactory->create($news, true);

            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

            return $this->redirectToRoute("app_admin_news_index");
        }

        return $this->render('admin/news/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(int $id, Request $request, NewsRepository $newsRepo, NewsFactoryInterface $newsFactory): Response
    {
        $news = $newsRepo->find($id);

        if ($news !== null) {
            $form = $this->createForm(NewsForm::class, $news);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $news = $form->getData();
                $newsFactory->edit($news);

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

                return $this->redirectToRoute("app_admin_news_edit", ['id' => $news->getId()]);
            }
        }

        return $this->render('admin/news/edit.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(int $id, NewsRepository $newsRepo): Response
    {
        $news = $newsRepo->find($id);

        if ($news !== null) {

            $newsRepo->remove($news, true);

            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
        } else {

            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_news_index");
    }

    #[Route('/{id}/publish', name: 'publish')]
    public function publish(int $id, NewsRepository $newsRepo, NewsServiceInterface $newsService): Response
    {
        $news = $newsRepo->find($id);

        if ($news !== null) {

            $status = $newsService->publish($news);

            if ($status) {
                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
            } else {
                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::MSG_WARNING);
            }
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_news_index");
    }

    #[Route('/{id}/unpublish', name: 'unpublish')]
    public function unpublish(int $id, NewsRepository $newsRepo, NewsServiceInterface $newsService): Response
    {
        $news = $newsRepo->find($id);

        if ($news !== null) {

            $status = $newsService->unpublish($news);

            if ($status) {
                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
            } else {
                $this->addFlash(FlashMessageService::TYPE_WARNING, FlashMessageService::MSG_WARNING);
            }
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_news_index");
    }
}
