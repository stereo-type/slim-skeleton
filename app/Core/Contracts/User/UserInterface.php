<?php

declare(strict_types=1);

namespace App\Core\Contracts\User;

use DateTime;
use Doctrine\Common\Collections\Collection;

interface UserInterface
{
    public function getId(): int;

    public function getPassword(): string;

    public function setVerifiedAt(DateTime $verifiedAt): static;

    public function isTwoFactor(): bool;

    public function getRoles(): array;
}
