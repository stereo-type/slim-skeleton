<?php

namespace App\Core\Entity;

use App\Core\Enum\PermissionValue;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table('role_permissions')]
#[ORM\UniqueConstraint(name: 'video_unique_idx', columns: ['role_id', 'permission_id'])]
class RolePermission
{
    #[ORM\Id, ORM\Column(options: ['unsigned' => true]), ORM\GeneratedValue]
    private int $id;

    #[Orm\Column(type: 'string', enumType: PermissionValue::class)]
    private PermissionValue $value;

    #[ORM\ManyToOne(targetEntity: 'Permission', inversedBy: 'rolePermissions')]
    #[ORM\JoinColumn(nullable: false)]
    private Permission $permission;

    #[ORM\ManyToOne(targetEntity: 'Role', inversedBy: 'rolePermissions')]
    #[ORM\JoinColumn(nullable: false)]
    private Role $role;

}