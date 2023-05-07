<?php

namespace App\Service\Email;

use Symfony\Component\Security\Core\User\UserInterface;

interface MailerServiceInterface
{
    public function sendManualResetPassword(UserInterface $user, string $plainPassword): bool;
}