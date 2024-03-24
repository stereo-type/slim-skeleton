<?php

namespace App\Core\Repository\User;

use App\Core\Entity\Role;
use App\Core\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    /**
     * Получить всех пользователей с определенной ролью.
     *
     * @param Role $role Роль
     *
     * @return User[] Массив пользователей с указанной ролью
     */
    public function findByRole(Role $role): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.roleAssignments', 'ra')
            ->andWhere('ra.role = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult();
    }
}
