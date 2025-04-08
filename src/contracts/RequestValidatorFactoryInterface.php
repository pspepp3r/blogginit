<?php

declare(strict_types = 1);

namespace Src\Contracts;

interface RequestValidatorFactoryInterface
{
    public function make(string $class): RequestValidatorInterface;
}
