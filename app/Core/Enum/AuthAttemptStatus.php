<?php

declare(strict_types = 1);

namespace App\Core\Enum;

enum AuthAttemptStatus
{
    case FAILED;
    case TWO_FACTOR_AUTH;
    case SUCCESS;
}
