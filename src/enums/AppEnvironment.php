<?php

declare(strict_types=1);

namespace Src\Enums;

enum AppEnvironment: string
{
    case Production = 'production';
    case Development = 'development';

    public static function isProduction(string $appEnvironment): bool
    {
        return self::tryFrom($appEnvironment) === self::Production;
    }

    public static function isDevelopment(string $appEnvironment): bool
    {
        return self::tryFrom($appEnvironment) === self::Development;
    }
}
