<?php

declare(strict_types = 1);

namespace App\Core\Enum;

enum StorageDriver
{
    case Local;
    case Remote_DO;
}
