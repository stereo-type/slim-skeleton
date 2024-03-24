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

readonly class AdminController
{
    public function __construct(
        private Twig $twig,
        private RequestValidatorFactoryInterface $requestValidatorFactory,
        private AuthInterface $auth,
        private ResponseFormatter $responseFormatter
    ) {
    }

    public function index(Request $request, Response $response): Response
    {
        $categories = [];


        return $this->twig->render(
            $response,
            'admin.twig',
            $categories
        );
    }
}
