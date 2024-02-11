<?php

declare(strict_types = 1);

namespace App\Core\Mail;

use App\Core\Config;
use App\Core\Entity\User;
use App\Core\SignedUrl;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;

class SignupEmail
{
    public function __construct(
        private readonly Config $config,
        private readonly MailerInterface $mailer,
        private readonly BodyRendererInterface $renderer,
        private readonly SignedUrl $signedUrl
    ) {
    }

    public function send(User $user): void
    {
        $email          = $user->getEmail();
        $expirationDate = new \DateTime('+30 minutes');
        $activationLink = $this->signedUrl->fromRoute(
            'verify',
            ['id' => $user->getId(), 'hash' => sha1($email)],
            $expirationDate
        );


        throw new \RuntimeException('exception');
        $message = (new TemplatedEmail())
            ->from($this->config->get('mailer.from'))
            ->to($email)
            ->subject('Welcome to Expennies')
            ->htmlTemplate('emails/signup.html.twig')
            ->context(
                [
                    'activationLink' => $activationLink,
                    'expirationDate' => $expirationDate,
                ]
            );

        $this->renderer->render($message);

        $this->mailer->send($message);
    }
}
