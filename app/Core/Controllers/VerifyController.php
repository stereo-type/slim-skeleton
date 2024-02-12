<?php

declare(strict_types = 1);

namespace App\Core\Controllers;

use App\Core\Constants\ServerStatus;
use App\Core\Contracts\User\UserProviderServiceInterface;
use App\Core\Entity\User;
use App\Core\Mail\SignupEmail;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class VerifyController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly UserProviderServiceInterface $userProviderService,
        private readonly SignupEmail $signupEmail
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($request->getAttribute('user')->getVerifiedAt()) {
            return $response->withHeader('Location', '/')->withStatus(ServerStatus::REDIRECT);
        }

        return $this->twig->render($response, 'auth/verify.twig');
    }

    public function verify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $user = $request->getAttribute('user');

        if (! hash_equals((string) $user->getId(), $args['id'])
            || ! hash_equals(sha1($user->getEmail()), $args['hash'])) {
            throw new RuntimeException('Verification failed');
        }

        if (! $user->getVerifiedAt()) {
            $this->userProviderService->verifyUser($user);
        }

        return $response->withHeader('Location', '/')->withStatus(ServerStatus::REDIRECT);
    }

    public function resend(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->signupEmail->send($request->getAttribute('user'));

        return $response;
    }
}
