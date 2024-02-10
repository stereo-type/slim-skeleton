<?php

declare(strict_types = 1);

namespace App\Core\RequestValidators;

use App\Core\Contracts\RequestValidatorInterface;
use App\Core\Exception\ValidationException;
use Valitron\Validator;

class ResetPasswordRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['password', 'confirmPassword'])->message('Required field');
        $v->rule('equals', 'confirmPassword', 'password')->label('Confirm Password');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
