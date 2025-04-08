<?php

declare(strict_types=1);

namespace Src\Data_objects;

class RegisterUserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password
    ) {}
}
