<?php

namespace App\Service\Admin;

interface AdminDashboardInterface
{
    public function getUsersCount(): int;

    public function getNewsCount(): int;
}
