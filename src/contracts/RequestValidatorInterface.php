<?php

declare(strict_types = 1);

namespace Src\Contracts;

interface RequestValidatorInterface
{
    public function validate(array $data): array;
}
