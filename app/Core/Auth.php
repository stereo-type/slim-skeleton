<?php

declare(strict_types = 1);

namespace App\Core;

use App\Core\Contracts\SessionInterface;
use App\Core\Contracts\User\AuthInterface;
use App\Core\Contracts\User\UserInterface;
use App\Core\Contracts\User\UserProviderServiceInterface;
use App\Core\DataObjects\RegisterUserData;
use App\Core\Enum\AuthAttemptStatus;
use App\Core\Mail\SignupEmail;
use App\Core\Mail\TwoFactorAuthEmail;
use App\Core\Services\UserLoginCodeService;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Auth implements AuthInterface
{
    private ?UserInterface $user = null;

    public function __construct(
        private readonly UserProviderServiceInterface $userProvider,
        private readonly SessionInterface $session,
        private readonly SignupEmail $signupEmail,
        private readonly TwoFactorAuthEmail $twoFactorAuthEmail,
        private readonly UserLoginCodeService $userLoginCodeService
    ) {
    }

    public function user(): ?UserInterface
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $userId = $this->session->get('user');

        if (! $userId) {
            return null;
        }

        $user = $this->userProvider->getById($userId);

        if (! $user) {
            return null;
        }

        $this->user = $user;

        return $this->user;
    }

    /**
     * @param  array  $credentials
     * @return AuthAttemptStatus
     * @throws Exception|TransportExceptionInterface
     */
    public function attemptLogin(array $credentials): AuthAttemptStatus
    {
        $user = $this->userProvider->getByCredentials($credentials);

        if (! $user || ! $this->checkCredentials($user, $credentials)) {
            return AuthAttemptStatus::FAILED;
        }

        if ($user->isTwoFactor()) {
            $this->startLoginWith2FA($user);

            return AuthAttemptStatus::TWO_FACTOR_AUTH;
        }

        $this->logIn($user);

        return AuthAttemptStatus::SUCCESS;
    }

    public function checkCredentials(UserInterface $user, array $credentials): bool
    {
        return password_verify($credentials['password'], $user->getPassword());
    }

    public function logOut(): void
    {
        $this->session->forget('user');
        $this->session->regenerate();

        $this->user = null;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function register(RegisterUserData $data): UserInterface
    {
        $user = $this->userProvider->createUser($data);

        $this->logIn($user);

        $this->signupEmail->send($user);

        return $user;
    }

    /**
     * @param  UserInterface  $user
     * @return void
     */
    public function logIn(UserInterface $user): void
    {
        $this->session->regenerate();
        $this->session->put('user', $user->getId());
        $this->user = $user;
    }

    /**
     * @param  UserInterface  $user
     * @return void
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function startLoginWith2FA(UserInterface $user): void
    {
        $this->session->regenerate();
        $this->session->put('2fa', $user->getId());

        $this->userLoginCodeService->deactivateAllActiveCodes($user);

        $this->twoFactorAuthEmail->send($this->userLoginCodeService->generate($user));
    }

    /**
     * @param  array  $data
     * @return bool
     */
    public function attemptTwoFactorLogin(array $data): bool
    {
        $userId = $this->session->get('2fa');

        if (! $userId) {
            return false;
        }

        $user = $this->userProvider->getById($userId);

        if (! $user || $user->getEmail() !== $data['email']) {
            return false;
        }

        if (! $this->userLoginCodeService->verify($user, $data['code'])) {
            return false;
        }

        $this->session->forget('2fa');

        $this->logIn($user);

        $this->userLoginCodeService->deactivateAllActiveCodes($user);

        return true;
    }
}
