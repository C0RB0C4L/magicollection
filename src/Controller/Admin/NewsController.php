<?php

namespace App\Controller\Admin;

use App\Form\NewsForm;
use App\Form\RegistrationForm;
use App\Form\UserEditForm;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use App\Security\UserSecurityManagerInterface;
use App\Service\Email\MailerServiceInterface;
use App\Service\FlashMessageService;
use App\Service\News\NewsFactoryInterface;
use App\Service\News\NewsServiceInterface;
use App\Service\User\UserFactoryInterface;
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
    public function edit(int $id): Response
    {
        return $this->redirectToRoute("app_admin_news_index");
    }

    #[Route('/{id}/publish', name: 'publish')]
    public function publish(int $id, NewsServiceInterface $newsService): Response
    {
        $status = $newsService->publish($id);

        if ($status) {
            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_news_index");
    }

    #[Route('/{id}/unpublish', name: 'unpublish')]
    public function unpublish(int $id, NewsServiceInterface $newsService): Response
    {
        $status = $newsService->unpublish($id);

        if ($status) {
            $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);
        } else {
            $this->addFlash(FlashMessageService::TYPE_ERROR, FlashMessageService::MSG_ERROR);
        }

        return $this->redirectToRoute("app_admin_news_index");
    }
}
