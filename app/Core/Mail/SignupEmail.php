<?php

declare(strict_types = 1);

namespace App\Core\Mail;

use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;
use App\Core\Config;
use App\Core\Entity\User;
use App\Core\SignedUrl;

readonly class SignupEmail
{
    public function __construct(
        private Config $config,
        private MailerInterface $mailer,
        private BodyRendererInterface $renderer,
        private SignedUrl $signedUrl
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(User $user): void
    {
        $email          = $user->getEmail();
        $expirationDate = new DateTime('+30 minutes');
        $activationLink = $this->signedUrl->fromRoute(
            'verify',
            ['id' => $user->getId(), 'hash' => sha1($email)],
            $expirationDate
        );

        $message = (new TemplatedEmail())
            ->from($this->config->get('mailer.from'))
            ->to($email)
            ->subject('Welcome to '.$this->config->get('app_name'))
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
