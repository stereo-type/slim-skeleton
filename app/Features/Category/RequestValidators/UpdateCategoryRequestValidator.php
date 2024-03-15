<?php

declare(strict_types = 1);

namespace App\Features\Category\RequestValidators;

use App\Core\Contracts\RequestValidatorInterface;
use App\Core\Exception\ValidationException;
use Valitron\Validator;

class UpdateCategoryRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', 'name')->message('Required field');
        $v->rule('lengthMax', 'name', 50);

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
