<?php

namespace App\Service;

use App\Entity\AccountCreationRequest;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface MailerServiceInterface
{
    // email to the user
    public function sendAccountCreationRequestEmail(AccountCreationRequest $accountCreationRequest): bool;

    // email to the user
    public function sendAccountCreationRequestConfirmed(AccountCreationRequest $accountCreationRequest): bool;

    // email to the user
    public function sendManualPasswordResetEmail(UserInterface $user, string $plainPassword): bool;
}
