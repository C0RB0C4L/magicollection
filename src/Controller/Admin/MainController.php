<?php

namespace App\Controller\Admin;

use App\Service\Admin\AdminDashboardInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app_admin_')]
class MainController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(AdminDashboardInterface $dashboard): Response
    {
        $userCount = $dashboard->getUsersCount();

        return $this->render('admin/index.html.twig', [
            "userCount" => $userCount
        ]);
    }
}
