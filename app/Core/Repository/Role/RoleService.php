<?php

namespace App\Core\Repository\Role;

use App\Core\Contracts\User\UserInterface;
use App\Core\Entity\Role;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;


readonly class RoleService
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param Connection $connection
     * @return void
     * @throws Exception
     */
    public static function create_default_roles(Connection $connection): void
    {
        $now = date('Y-m-d H:i:s', time());
        $connection->insert('roles', ['name' => Role::ADMIN, 'created_at' => $now, 'updated_at' => $now]);
        $connection->insert('roles', ['name' => Role::CUSTOMER, 'created_at' => $now, 'updated_at' => $now]);
        $connection->insert('roles', ['name' => Role::MANAGER, 'created_at' => $now, 'updated_at' => $now]);
    }

    /**
     * @param Connection $connection
     * @return void
     * @throws Exception
     */
    public static function remove_default_roles(Connection $connection): void
    {
        $connection->delete('roles', ['name' => Role::ADMIN]);
        $connection->delete('roles', ['name' => Role::CUSTOMER]);
        $connection->delete('roles', ['name' => Role::MANAGER]);
    }


    public function createRole($name): Role
    {
        $role = new Role();
        $role->setName($name);

        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $role;
    }


    public function isAdmin(UserInterface $user): bool
    {
        $roles = $user->getRoles();
        if ($roles->isEmpty()) {
            return false;
        }
        foreach ($roles as $role) {
            if ($role instanceof Role) {
                if ($role->getName() == Role::ADMIN) {
                    return true;
                }
            }
        }
        return false;
    }


}
