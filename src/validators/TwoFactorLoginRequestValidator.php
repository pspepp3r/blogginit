<?php

declare(strict_types = 1);

namespace Src\Validators;

use Valitron\Validator;
use Src\Errors\ValidationException;
use Src\Contracts\RequestValidatorInterface;

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
