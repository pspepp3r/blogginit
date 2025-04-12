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

        $v->rule('required', ['email', 'name'])->message('Required field');
        $v->rule('email', 'email');
        $v->rule(
            fn($field, $value, $params, $fields) => !$this->entityManager->getRepository(User::class)
                ->count(['email' => $value, 'name' => $data['name']]),
            'email'
        )->message('User with the given email address already exists');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
