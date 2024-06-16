<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        #[Autowire('%admin_email%')] private string $adminEmail,
        private readonly MailerInterface $mailer
    ) {

    }

    public function sendEmailForInscription(User $user, Event $event): void {
        $email = (new Email())
            ->from($this->adminEmail)
            ->to($user->getEmail())
            ->text("
                Bonjour {$user->getFirstname()},
                Votre inscription pour l'événement {$event->getTitle()}, qui aura lieu le {$event->getDateTime()->format('d/m/Y à H:i')} a été effectuée.
                Cordialement,
            ");

        $this->mailer->send($email);
    }

    public function sendEmailForAnnulation(User $user, Event $event): void {
        $email = (new Email())
            ->from($this->adminEmail)
            ->to($user->getEmail())
            ->text("
                Bonjour {$user->getFirstname()},
                L'événement {$event->getTitle()}, qui avait lieu le {$event->getDateTime()->format('d/m/Y à H:i')}, a été annulé.
                Cordialement,
            ");

        $this->mailer->send($email);
    }
}