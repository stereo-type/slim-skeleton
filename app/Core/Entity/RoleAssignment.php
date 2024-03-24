<?php

namespace App\Core\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

use App\Core\Contracts\Role\RoleAssignmentInterface;

#[ORM\Entity]
#[ORM\Table('role_assignments')]
#[ORM\UniqueConstraint(name: 'video_unique_idx', columns: ['user_id', 'role_id'])]
class RoleAssignment implements RoleAssignmentInterface
{
    #[ORM\Id, ORM\Column(options: ['unsigned' => true]), ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: 'Role', inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    private Role $role;


    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function addUserRole(User $user, Role $role, bool $flush = true): void
    {
        $assignment = new RoleAssignment($this->entityManager);
        $assignment->setRole($role);
        $assignment->setUser($user);
        $this->entityManager->persist($assignment);
        $this->entityManager->flush();
    }

    public function removeUserRole(User $user, Role $role, bool $flush = true): void
    {
        $assignment = $this->entityManager->getRepository(self::class)->findOneBy(['user' => $user, 'role' => $role]);
        if ($assignment != null) {
            $this->entityManager->remove($assignment);
            $this->entityManager->flush();
        }
    }

    public function changeUserRole(User $user, Role $oldRole, Role $newRole, bool $flush = true): void
    {
        $this->removeUserRole($user, $oldRole, false);
        $this->addUserRole($user, $newRole, $flush);
    }

    public function getUserRoles(User $user): array
    {
        $assignments = $this->entityManager->getRepository(self::class)->findBy(['user' => $user]);
        return array_map(static fn(self $a) => $a->getRole(), $assignments);
    }
}