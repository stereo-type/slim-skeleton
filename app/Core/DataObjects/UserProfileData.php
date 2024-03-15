<?php

declare(strict_types = 1);

namespace App\Core\DataObjects;
readonly class UserProfileData
{
    public function __construct(
        public string $email,
        public string $name,
        public bool $twoFactor
    ) {
    }
}
