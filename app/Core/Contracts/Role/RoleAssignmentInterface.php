<?php

namespace App\Core\Contracts\Role;

use App\Core\Entity\Role;
use App\Core\Entity\User;
use Doctrine\Common\Collections\Collection;

interface RoleAssignmentInterface
{

    public function getRole(): Role;

    public function addUserRole(User $user, Role $role, bool $flush = true): void;

    public function removeUserRole(User $user, Role $role, bool $flush = true): void;

    public function changeUserRole(User $user, Role $oldRole, Role $newRole, bool $flush = true): void;

    public function getUserRoles(User $user): array;
}