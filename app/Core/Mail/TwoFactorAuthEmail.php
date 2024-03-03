<?php

declare(strict_types = 1);

namespace App\Core\Mail;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\BodyRendererInterface;
use App\Core\Config;
use App\Core\Entity\UserLoginCode;

readonly class TwoFactorAuthEmail
{
    public function __construct(
        private Config $config,
        private MailerInterface $mailer,
        private BodyRendererInterface $renderer
    ) {
    }

    /**
     * @param  UserLoginCode  $userLoginCode
     * @return void
     * @throws TransportExceptionInterface
     */
    public function send(UserLoginCode $userLoginCode): void
    {
        $email   = $userLoginCode->getUser()->getEmail();
        $message = (new TemplatedEmail())
            ->from($this->config->get('mailer.from'))
            ->to($email)
            ->subject('Your Verification Code')
            ->htmlTemplate('emails/two_factor.html.twig')
            ->context(
                [
                    'code' => $userLoginCode->getCode(),
                ]
            );

        $this->renderer->render($message);

        $this->mailer->send($message);
    }
}
