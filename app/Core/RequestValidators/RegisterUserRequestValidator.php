<?php

declare(strict_types = 1);

namespace App\Core\RequestValidators;

use App\Core\Contracts\EntityManagerServiceInterface;
use App\Core\Contracts\RequestValidatorInterface;
use App\Core\Entity\User;
use App\Core\Exception\ValidationException;
use Valitron\Validator;

readonly class RegisterUserRequestValidator implements RequestValidatorInterface
{
    public function __construct(private EntityManagerServiceInterface $entityManager)
    {
    }

    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['name', 'email', 'password', 'confirmPassword'])->message('Required field');
        $v->rule('email', 'email');
        $v->rule('equals', 'confirmPassword', 'password')->label('Confirm Password');
        $v->rule(
            fn($field, $value, $params, $fields) => ! $this->entityManager->getRepository(User::class)->count(
                ['email' => $value]
            ),
            'email'
        )->message('User with the given email address already exists');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
