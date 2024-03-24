<?php

declare(strict_types = 1);

namespace App\Core\Repository\User;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Contracts\User\UserProviderServiceInterface;
use App\Core\Entity\PasswordReset;
use App\Core\Entity\User;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

readonly class PasswordResetRepository
{
    public function __construct(
        private EntityManagerServiceInterface $entityManagerService,
        private UserProviderServiceInterface $userProviderService
    ) {
    }

    /**
     * @throws Exception
     */
    public function generate(string $email): PasswordReset
    {
        $passwordReset = new PasswordReset();

        $passwordReset->setToken(bin2hex(random_bytes(32)));
        $passwordReset->setExpiration(new DateTime('+30 minutes'));
        $passwordReset->setEmail($email);

        $this->entityManagerService->sync($passwordReset);

        return $passwordReset;
    }

    public function deactivateAllPasswordResets(string $email): void
    {
        $this->entityManagerService
            ->getRepository(PasswordReset::class)
            ->createQueryBuilder('pr')
            ->update()
            ->set('pr.isActive', '0')
            ->where('pr.email = :email')
            ->andWhere('pr.isActive = 1')
            ->setParameter('email', $email)
            ->getQuery()
            ->execute();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByToken(string $token): ?PasswordReset
    {
        return $this->entityManagerService
            ->getRepository(PasswordReset::class)
            ->createQueryBuilder('pr')
            ->select('pr')
            ->where('pr.token = :token')
            ->andWhere('pr.isActive = :active')
            ->andWhere('pr.expiration > :now')
            ->setParameters(
                [
                    'token'  => $token,
                    'active' => true,
                    'now'    => new DateTime(),
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updatePassword(User $user, string $password): void
    {
        $this->entityManagerService->wrapInTransaction(function () use ($user, $password) {
            $this->deactivateAllPasswordResets($user->getEmail());

            $this->userProviderService->updatePassword($user, $password);
        });
    }
}
