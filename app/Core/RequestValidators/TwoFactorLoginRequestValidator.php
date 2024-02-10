<?php

declare(strict_types = 1);

namespace App\Core\RequestValidators;

use App\Core\Contracts\RequestValidatorInterface;
use App\Core\Exception\ValidationException;
use Valitron\Validator;

class TwoFactorLoginRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['email', 'code'])->message('Required field');
        $v->rule('email', 'email');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
