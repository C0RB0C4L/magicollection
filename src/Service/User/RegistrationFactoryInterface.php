<?php

namespace App\Service\User;

use App\Entity\AccountCreationRequest;
use Symfony\Component\Security\Core\User\UserInterface;

interface RegistrationFactoryInterface
{
    public function createAccountRequest(UserInterface $user): ?AccountCreationRequest;
}
