<?php

declare(strict_types = 1);

namespace App\Core\Services;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\DataObjects\UserProfileData;
use App\Core\Entity\User;

class UserProfileService
{
    public function __construct(private readonly EntityManagerServiceInterface $entityManagerService)
    {
    }

    public function update(User $user, UserProfileData $data): void
    {
        $user->setName($data->name);
        $user->setTwoFactor($data->twoFactor);

        $this->entityManagerService->sync($user);
    }

    public function get(int $userId): UserProfileData
    {
        $user = $this->entityManagerService->find(User::class, $userId);

        return new UserProfileData($user->getEmail(), $user->getName(), $user->hasTwoFactorAuthEnabled());
    }
}
