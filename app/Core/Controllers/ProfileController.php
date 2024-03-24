<?php

declare(strict_types = 1);

namespace App\Core\Controllers;

use App\Core\Contracts\RequestValidatorFactoryInterface;
use App\Core\DataObjects\UserProfileData;
use App\Core\Repository\User\PasswordResetRepository;
use App\Core\RequestValidators\UpdatePasswordRequestValidator;
use App\Core\RequestValidators\UpdateProfileRequestValidator;
use App\Core\Services\UserProfileService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class ProfileController
{
    public function __construct(
        private Twig $twig,
        private RequestValidatorFactoryInterface $requestValidatorFactory,
        private UserProfileService $userProfileService,
        private PasswordResetRepository $passwordResetService
    ) {
    }

    /**
     * @param  Request  $request
     * @param  Response  $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'profile/index.twig',
            ['profileData' => $this->userProfileService->get($request->getAttribute('user')->getId())]
        );
    }

    public function update(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $data = $this->requestValidatorFactory->make(UpdateProfileRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->userProfileService->update(
            $user,
            new UserProfileData($user->getEmail(), $data['name'], (bool) ($data['twoFactor'] ?? false))
        );

        return $response;
    }

    public function updatePassword(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $data = $this->requestValidatorFactory->make(UpdatePasswordRequestValidator::class)->validate(
            $request->getParsedBody() + ['user' => $user]
        );

        $this->passwordResetService->updatePassword($user, $data['newPassword']);

        return $response;
    }
}
