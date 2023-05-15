<?php

namespace App\Service;

use App\Entity\AccountCreationRequest;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;

final class MailerService implements MailerServiceInterface
{
    private MailerInterface $mailer;

    private string $noReplySender;

    public function __construct($noReplySender, MailerInterface $mailer)
    {
        $this->noReplySender = $noReplySender;
        $this->mailer = $mailer;
    }

    private function send(TemplatedEmail|Email $email)
    {
        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {

            return false;
        }
    }

    public function sendAccountCreationRequestEmail(AccountCreationRequest $accountCreationRequest): bool
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->noReplySender))
            ->to($accountCreationRequest->getUser()->getEmail())
            ->subject("account creation request")
            ->htmlTemplate('email/account_creation_request.html.twig')
            ->context([
                'accountCreationRequest' => $accountCreationRequest
            ]);

        return $this->send($email);
    }

    public function sendAccountCreationRequestConfirmed(AccountCreationRequest $accountCreationRequest): bool
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->noReplySender))
            ->to($accountCreationRequest->getUser()->getEmail())
            ->subject("account creation request")
            ->htmlTemplate('email/account_creation_confirmed.html.twig')
            ->context([
                'accountCreationRequest' => $accountCreationRequest
            ]);

        return $this->send($email);
    }

    public function sendManualPasswordResetEmail(UserInterface|User $user, string $plainPassword): bool
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->noReplySender))
            ->to($user->getEmail())
            ->subject("password reset manual")
            ->htmlTemplate('email/password_reset_manual.html.twig')
            ->context([
                'plainPassword' => $plainPassword
            ]);

        return $this->send($email);
    }
}
