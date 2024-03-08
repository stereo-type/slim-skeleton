<?php

declare(strict_types = 1);

namespace App\Core\Services;

readonly class HashService
{
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
