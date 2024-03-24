<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use RuntimeException;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Slim\Views\Twig;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use App\Core\Mail\SignupEmail;
use App\Core\Enum\ServerStatus;
use App\Core\Contracts\User\UserProviderServiceInterface;

readonly class VerifyController
{
    public function __construct(
        private Twig $twig,
        private UserProviderServiceInterface $userProviderService,
        private SignupEmail $signupEmail
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
            return $response->withHeader('Location', '/')->withStatus(ServerStatus::REDIRECT->value);
        }

        return $this->twig->render($response, 'auth/verify.twig');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function verify(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $request->getAttribute('user');

        if (!hash_equals((string)$user->getId(), $args['id']) ||
            !hash_equals(sha1($user->getEmail()), $args['hash'])) {
            throw new RuntimeException('Verification failed');
        }

        if (!$user->getVerifiedAt()) {
            $this->userProviderService->verifyUser($user);
        }

        return $response->withHeader('Location', '/')->withStatus(ServerStatus::REDIRECT->value);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function resend(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->signupEmail->send($request->getAttribute('user'));

        return $response;
    }
}
