<?php

namespace App\Service\Admin;

use App\Repository\NewsRepository;
use App\Repository\UserRepository;

final class AdminDashboard implements AdminDashboardInterface
{
    private UserRepository $userRepo;

    private NewsRepository $newsRepo;

    public function __construct(UserRepository $userRepo, NewsRepository $newsRepo)
    {
        $this->userRepo = $userRepo;
        $this->newsRepo = $newsRepo;
    }

    public function getUsersCount(): int
    {
        return $this->userRepo->countUsers();
    }

    public function getNewsCount(): int
    {
        return $this->newsRepo->countNews();
    }
}
