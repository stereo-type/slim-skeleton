<?php

declare(strict_types=1);

namespace App\Core\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use App\Core\Contracts\User\OwnableInterface;
use App\Core\Contracts\User\UserInterface;
use App\Core\Entity\Traits\HasTimestamps;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity, Table('users')]
#[HasLifecycleCallbacks]
class User implements UserInterface
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[Column]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[Assert\Email]
    #[Column]
    private string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    #[Column]
    private string $password;

    #[Column(name: 'two_factor', options: ['default' => false])]
    private bool $twoFactor;

    #[Column(name: 'verified_at', nullable: true)]
    private ?DateTime $verifiedAt;

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
}
