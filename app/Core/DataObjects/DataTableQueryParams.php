<?php

declare(strict_types = 1);

namespace App\Core\DataObjects;

readonly class DataTableQueryParams
{
    public function __construct(
        public int $start,
        public int $length,
        public string $orderBy,
        public string $orderDir,
        public string $searchTerm,
        public int $draw
    ) {
    }
}
