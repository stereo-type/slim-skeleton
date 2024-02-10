<?php

declare(strict_types = 1);

namespace App\Core\Contracts;

interface RequestValidatorInterface
{
    public function validate(array $data): array;
}
