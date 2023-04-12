<?php

namespace App\Service\User;

use App\Entity\User;

interface UserFactoryInterface {

    /**
     * The user will be "active" and "verified" by default.
     * The `$username` and `$email` must be unique.
     */
    public function createSimpleUser(string $username, string $email, string $password): ?User;
}