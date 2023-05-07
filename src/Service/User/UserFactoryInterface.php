<?php

namespace App\Service\User;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserFactoryInterface
{

    /**
     * The `$username` and `$email` must be unique.
     */
    public function createSimpleUser(string $username, string $email, string $password, bool $active, bool $verified): ?User;

    public function createMasterUser(string $username, string $email, string $password, bool $active, bool $verified): ?User;

    public function accountCreationRequest(UserInterface|User $user);
}
