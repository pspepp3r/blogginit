<?php

declare(strict_types = 1);

namespace Src\Services;

class HashService
{
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
