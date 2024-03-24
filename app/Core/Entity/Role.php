<?php

namespace App\Core\Entity;


use App\Core\Entity\Traits\HasTimestamps;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
#[ORM\Table('roles')]
#[ORM\HasLifecycleCallbacks]
class Role
{
    use HasTimestamps;


    public const ADMIN = 'admin';

    public const MANAGER = 'manager';

    public const CUSTOMER = 'customer';


    #[ORM\Id, ORM\Column(options: ['unsigned' => true]), ORM\GeneratedValue]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[ORM\Column(length: '64', unique: true)]
    private string $name;

    #[ORM\Column(length: '255', nullable: true)]
    private string $fullname;

    #[ORM\Column(length: '64', nullable: true)]
    private string $archetype;

    #[ORM\Column(length: '255', nullable: true)]
    private string $description;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: 'RoleAssignment', cascade: ["persist", "remove"])]
    private Collection $assignments;

    #[ORM\OneToMany(mappedBy: 'permission', targetEntity: 'RolePermission', cascade: ["persist", "remove"])]
    private Collection $permissions;


    public function getId(): int
    {
        return $this->id;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function getArchetype(): string
    {
        return $this->archetype;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }


}
