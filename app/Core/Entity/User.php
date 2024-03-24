<?php

declare(strict_types=1);

namespace App\Core\Entity;

use App\Core\Contracts\Role\RoleAssignmentInterface;
use App\Core\Contracts\User\OwnableInterface;
use App\Core\Contracts\User\UserInterface;
use App\Core\Entity\Traits\HasTimestamps;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity, ORM\Table('users')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface
{
    use HasTimestamps;

    #[ORM\Id, ORM\Column(options: ['unsigned' => true]), ORM\GeneratedValue]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[ORM\Column]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[Assert\Email]
    #[ORM\Column]
    private string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[ORM\Column]
    private string $password;

    #[ORM\Column(name: 'two_factor', options: ['default' => false])]
    private bool $twoFactor;

    #[ORM\Column(name: 'verified_at', nullable: true)]
    private ?DateTime $verifiedAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'RoleAssignment', cascade: ['persist', 'remove'])]
    private Collection $assignments;

    public function __construct()
    {
        $this->twoFactor = false;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function canManage(OwnableInterface $entity): bool
    {
        return $this->getId() === $entity->getUser()->getId();
    }

    public function getVerifiedAt(): ?DateTime
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(DateTime $verifiedAt): static
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }

    public function isTwoFactor(): bool
    {
        return $this->twoFactor;
    }

    public function setTwoFactor(bool $twoFactor): User
    {
        $this->twoFactor = $twoFactor;

        return $this;
    }

    public function getRolesAssignments(): Collection
    {
        return $this->assignments;
    }

    public function getRoles(): array
    {
        return array_map(static fn(RoleAssignmentInterface $e) => $e->getRole(), $this->assignments->toArray());
    }

}
