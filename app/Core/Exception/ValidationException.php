<?php

declare(strict_types=1);

namespace App\Core\Exception;

use App\Core\Enum\ServerStatus;
use RuntimeException;
use Throwable;

class ValidationException extends RuntimeException
{
    public function __construct(
        public readonly array $errors,
        string $message = 'Validation Error(s)',
        int $code = ServerStatus::VALIDATION_ERROR->value,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
