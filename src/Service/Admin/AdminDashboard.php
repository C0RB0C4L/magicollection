<?php

namespace App\Service\Admin;

use App\Repository\UserRepository;

final class AdminDashboard implements AdminDashboardInterface
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getUsersCount(): int
    {
        return $this->userRepo->countUsers();
    }
}
