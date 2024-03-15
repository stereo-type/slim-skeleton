<?php

declare(strict_types = 1);

namespace App\Core\Services;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Entity\User;
use App\Core\Entity\UserLoginCode;
use DateTime;
use Exception;

readonly class UserLoginCodeService
{
    public function __construct(private EntityManagerServiceInterface $entityManagerService)
    {
    }

    /**
     * @throws Exception
     */
    public function generate(User $user): UserLoginCode
    {
        $userLoginCode = new UserLoginCode();

        $code = random_int(100000, 999999);

        $userLoginCode->setCode((string) $code);
        $userLoginCode->setExpiration(new DateTime('+10 minutes'));
        $userLoginCode->setUser($user);

        $this->entityManagerService->sync($userLoginCode);

        return $userLoginCode;
    }

    public function verify(User $user, string $code): bool
    {
        $userLoginCode = $this->entityManagerService->getRepository(UserLoginCode::class)->findOneBy(
            ['user' => $user, 'code' => $code, 'isActive' => true]
        );

        if (! $userLoginCode) {
            return false;
        }

        if ($userLoginCode->getExpiration() <= new DateTime()) {
            return false;
        }

        return true;
    }

    public function deactivateAllActiveCodes(User $user): void
    {
        $this->entityManagerService->getRepository(UserLoginCode::class)
            ->createQueryBuilder('c')
            ->update()
            ->set('c.isActive', '0')
            ->where('c.user = :user')
            ->andWhere('c.isActive = 1')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
