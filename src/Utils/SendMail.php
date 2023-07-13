<?php

namespace App\Utils;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class SendMail
{
    public function __construct(
        private MailerInterface $mailer,
    )
    {

    }

    /**
     * @throws TransportExceptionInterface
     */
    public function TemplatedEmail(
        $to,
        $from,
        $subject,
        $htmlTemplate,
        $variables
    ): void
    {
        $emailAdmin = (new TemplatedEmail())
            ->from('no-reply@sortir.com')
            ->to($to)
            ->replyTo($from)
            ->subject("🍃 Sortir.com | $subject")
            ->htmlTemplate($htmlTemplate)
            ->context($variables);

        $this->mailer->send($emailAdmin);
    }
}