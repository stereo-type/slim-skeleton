<?php

declare(strict_types = 1);

namespace App\Core\Exception;

use App\Core\Enum\ServerStatus;
use RuntimeException;
use Throwable;

class PermissionException extends RuntimeException
{
    public function __construct(
        public readonly array $permissions,
        string $message = 'Permission Error(s)',
        int $code = ServerStatus::FORBIDDEN->value,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

}
