<?php

declare(strict_types = 1);

namespace App\Core\Controllers;

use App\Core\Constants\ServerStatus;
use App\Core\Contracts\RequestValidatorFactoryInterface;
use App\Core\Contracts\User\AuthInterface;
use App\Core\DataObjects\RegisterUserData;
use App\Core\Enum\AuthAttemptStatus;
use App\Core\Exception\ValidationException;
use App\Core\RequestValidators\RegisterUserRequestValidator;
use App\Core\RequestValidators\TwoFactorLoginRequestValidator;
use App\Core\RequestValidators\UserLoginRequestValidator;
use App\Core\ResponseFormatter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly AuthInterface $auth,
        private readonly ResponseFormatter $responseFormatter
    ) {
    }

    public function loginView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    public function registerView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(RegisterUserRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->auth->register(
            new RegisterUserData($data['name'], $data['email'], $data['password'])
        );

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logIn(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $status = $this->auth->attemptLogin($data);

        if ($status === AuthAttemptStatus::FAILED) {
            throw new ValidationException(['password' => ['You have entered an invalid username or password']]);
        }

        if ($status === AuthAttemptStatus::TWO_FACTOR_AUTH) {
            return $this->responseFormatter->asJson($response, ['two_factor' => true]);
        }

        return $this->responseFormatter->asJson($response, []);
    }

    public function logOut(Response $response): Response
    {
        $this->auth->logOut();

        return $response->withHeader('Location', '/')->withStatus(ServerStatus::VALIDATION_ERROR);
    }

    public function twoFactorLogin(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(TwoFactorLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        if (! $this->auth->attemptTwoFactorLogin($data)) {
            throw new ValidationException(['code' => ['Invalid Code']]);
        }

        return $response;
    }
}
