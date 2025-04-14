<?php

declare(strict_types=1);

namespace Src\Validators;

use Src\Entities\User;
use Valitron\Validator;
use Doctrine\ORM\EntityManager;
use Src\Errors\ValidationException;
use Src\Contracts\RequestValidatorInterface;
use Src\Contracts\RequestValidatorFactoryInterface;

class UpdateProfileRequestValidator implements RequestValidatorInterface
{
    public function __construct(private readonly EntityManager $entityManager) {}

    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['name'])->message('Required field');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
