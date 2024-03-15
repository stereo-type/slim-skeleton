<?php

declare(strict_types = 1);

namespace App\Core\DataObjects;

readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
    }
}
