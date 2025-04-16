<?php

declare(strict_types=1);

namespace Src\Validators;

use Valitron\Validator;
use Src\Errors\ValidationException;
use Src\Contracts\RequestValidatorInterface;

class CreateBlogRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['title', 'content', 'category'])->message('Required field');
        $v->rule('lengthMax', 'title', 32);

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
