<?php

namespace App\Controller;

use App\Form\AlbumForm;
use App\Repository\AlbumRepository;
use App\Service\Album\AlbumFactoryInterface;
use App\Service\FlashMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/album', name: 'app_album_')]
class AlbumController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(AlbumRepository $albumRepo): Response
    {
        $currentUser = $this->getUser();

        $albums = $albumRepo->findBy(["user" => $currentUser]);

        return $this->render('album/index.html.twig', [
            'albums' => $albums,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, AlbumFactoryInterface $factory, RequestStack $stack): Response
    {
        $form = $this->createForm(AlbumForm::class);

        if ($request->isXmlHttpRequest() && $this->getUser()) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $albumToCreate = $form->getData();
                $user = $this->getUser();

                $factory->createAlbum($albumToCreate, $user);

                $this->addFlash(FlashMessageService::TYPE_SUCCESS, FlashMessageService::MSG_SUCCESS);

                return new JsonResponse(["status" => 1, "url" => $this->generateUrl("app_album_index")]);
            }

            $responseBody = $this->renderForm('account/modals/email_edit.html.twig', [
                'form' => $form
            ]);

            return new JsonResponse(["status" => 0, "body" => $responseBody->getContent()]);
        } else {


            if ($stack->getParentRequest() !== null) {

                return $this->render('album/modals/create.html.twig', [
                    "form" => $form->createView()
                ]);
            }

            return $this->redirectToRoute("app_album_index");
        }
    }
}
