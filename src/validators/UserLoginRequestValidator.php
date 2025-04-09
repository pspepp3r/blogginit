<?php

declare(strict_types=1);

namespace Src\Validators;

use Valitron\Validator;
use Doctrine\ORM\EntityManager;
use Src\Errors\ValidationException;
use Src\Contracts\RequestValidatorInterface;

class UserLoginRequestValidator implements RequestValidatorInterface
{
    public function __construct(private readonly EntityManager $entityManager) {}

    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['email', 'password'])->message('Required field');
        $v->rule('email', 'email');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
