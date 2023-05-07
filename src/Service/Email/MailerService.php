<?php

namespace App\Service\Email;

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

    private function sendEmail(TemplatedEmail|Email $email)
    {
        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {

            return false;
        }
    }

    public function sendManualResetPassword(UserInterface|User $user, string $plainPassword): bool
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->noReplySender))
            ->to($user->getEmail())
            ->subject("zer")
            ->htmlTemplate('email/reset_password_manual.html.twig')
            ->context([
                'plainPassword' => $plainPassword
            ]);

        return $this->sendEmail($email);
    }
}
