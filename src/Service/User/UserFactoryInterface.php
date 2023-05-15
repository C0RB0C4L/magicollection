<?php

namespace App\Service\User;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserFactoryInterface
{
    /**
     * `$user` is generated through the registration form
     */
    public function createUser(UserInterface|User $user, array $roles, bool $active, bool $verified): ?User;
}
