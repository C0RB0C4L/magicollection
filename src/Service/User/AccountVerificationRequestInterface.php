<?php

namespace App\Service\User;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface AccountVerificationRequestInterface
{
    public function create(UserInterface|User $user);
}
