<?php

namespace App\Core\Entity;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use App\Core\Enum\PermissionType;
use App\Core\Entity\Traits\HasTimestamps;

#[ORM\Entity, ORM\Table('permissions')]
#[ORM\HasLifecycleCallbacks]
class Permission
{
    use HasTimestamps;

    #[ORM\Id, ORM\Column(options: ['unsigned' => true]), ORM\GeneratedValue]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[Assert\Length(max: 127)]
    #[ORM\Column(length: '127', unique: true)]
    private string $name;

    #[ORM\Column]
    private string $description;

    #[ORM\Column(type: 'string', length: '16', enumType: PermissionType::class)]
    private PermissionType $type;


    #[ORM\OneToMany(mappedBy: 'permission', targetEntity: 'RolePermission', cascade: ["persist", "remove"])]
    private Collection $rolePermissions;


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): PermissionType
    {
        return $this->type;
    }

    public function getRolePermissions(): Collection
    {
        return $this->rolePermissions;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
