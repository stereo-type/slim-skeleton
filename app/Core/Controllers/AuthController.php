<?php

declare(strict_types = 1);

namespace App\Core\Controllers;

use App\Core\Contracts\RequestValidatorFactoryInterface;
use App\Core\Contracts\User\AuthInterface;
use App\Core\DataObjects\RegisterUserData;
use App\Core\Enum\AuthAttemptStatus;
use App\Core\Enum\ServerStatus;
use App\Core\Exception\ValidationException;
use App\Core\RequestValidators\RegisterUserRequestValidator;
use App\Core\RequestValidators\TwoFactorLoginRequestValidator;
use App\Core\RequestValidators\UserLoginRequestValidator;
use App\Core\ResponseFormatter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class AuthController
{
    public function __construct(
        private Twig $twig,
        private RequestValidatorFactoryInterface $requestValidatorFactory,
        private AuthInterface $auth,
        private ResponseFormatter $responseFormatter
    ) {
    }

    /**
     * @param  Response  $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function loginView(Response $response): Response
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    /**
     * @param  Response  $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
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

        return $response->withHeader('Location', '/')->withStatus(ServerStatus::REDIRECT->value);
    }

    public function logIn(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $status = $this->auth->attemptLogin($data);

        if ($status === AuthAttemptStatus::FAILED) {
            //TODO translate
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
        return $response->withHeader('Location', '/');
    }

    public function twoFactorLogin(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(TwoFactorLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );
        //TODO translate
        if (! $this->auth->attemptTwoFactorLogin($data)) {
            throw new ValidationException(['code' => ['Invalid Code']]);
        }

        return $response;
    }
}
