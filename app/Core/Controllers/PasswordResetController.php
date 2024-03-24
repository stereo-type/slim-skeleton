<?php

declare(strict_types = 1);

namespace App\Core\Controllers;

use App\Core\Contracts\RequestValidatorFactoryInterface;
use App\Core\Contracts\User\UserProviderServiceInterface;
use App\Core\Enum\ServerStatus;
use App\Core\Exception\ValidationException;
use App\Core\Mail\ForgotPasswordEmail;
use App\Core\Repository\User\PasswordResetRepository;
use App\Core\RequestValidators\ForgotPasswordRequestValidator;
use App\Core\RequestValidators\ResetPasswordRequestValidator;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class PasswordResetController
{
    public function __construct(
        private Twig $twig,
        private RequestValidatorFactoryInterface $requestValidatorFactory,
        private UserProviderServiceInterface $userProviderService,
        private PasswordResetRepository $passwordResetService,
        private ForgotPasswordEmail $forgotPasswordEmail
    ) {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function showForgotPasswordForm(Response $response): Response
    {
        return $this->twig->render($response, 'auth/forgot_password.twig');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function handleForgotPasswordRequest(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(ForgotPasswordRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $user = $this->userProviderService->getByCredentials($data);

        if ($user) {
            $this->passwordResetService->deactivateAllPasswordResets($data['email']);

            $passwordReset = $this->passwordResetService->generate($data['email']);

            $this->forgotPasswordEmail->send($passwordReset);
        }

        return $response;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws NonUniqueResultException
     */
    public function showResetPasswordForm(Response $response, array $args): Response
    {
        $passwordReset = $this->passwordResetService->findByToken($args['token']);

        if (! $passwordReset) {
            return $response->withHeader('Location', '/')->withStatus(ServerStatus::REDIRECT->value);
        }

        return $this->twig->render($response, 'auth/reset_password.twig', ['token' => $args['token']]);
    }

    /**
     * @param  Request  $request
     * @param  Response  $response
     * @param  array  $args
     * @return Response
     * @throws NonUniqueResultException
     */
    public function resetPassword(Request $request, Response $response, array $args): Response
    {
        $data = $this->requestValidatorFactory->make(ResetPasswordRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $passwordReset = $this->passwordResetService->findByToken($args['token']);

        if (! $passwordReset) {
            throw new ValidationException(['confirmPassword' => ['Invalid token']]);
        }

        $user = $this->userProviderService->getByCredentials(['email' => $passwordReset->getEmail()]);

        if (! $user) {
            throw new ValidationException(['confirmPassword' => ['Invalid token']]);
        }

        $this->passwordResetService->updatePassword($user, $data['password']);

        return $response;
    }
}
