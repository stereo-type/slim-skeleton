<?php

declare(strict_types = 1);

namespace App\Core\Contracts;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @mixin EntityManagerInterface
 */
interface EntityManagerServiceInterface
{
    public function __call(string $name, array $arguments);

    public function sync(?object $entity = null): void;

    public function delete(object $entity, bool $sync = false): void;

    public function clear(?string $entityName = null): void;

    public function enableUserAuthFilter(int $userId): void;
}
