<?php

declare(strict_types=1);

use Src\Enums\AppEnvironment;

$boolean = function (mixed $value) {

    if (in_array($value, ['true', 1, '1', true, 'yes'], true)) {

        return true;
    }

    return false;
};

$appEnv = $_ENV['APP_ENV'] ?? AppEnvironment::Production->value;
$formatAppName = strtolower(str_replace(' ', '_', $_ENV['APP_NAME']));

return [

    'app' => [

        'app_name' => $_ENV['APP_NAME'],
        'app_version' => $_ENV['APP_VERSION'] ?? '1.0',
        'app_environment' => $appEnv,
        'app_debug' => $boolean($_ENV['APP_DEBUG'] ?? 0)

    ],

    'db' => [

        'dev_mode' => AppEnvironment::isDevelopment($appEnv),
        'cache_dir' => STORAGE_PATH . '/cache/orm',
        'entity_dir' => ENTITY_PATH,
        'driver' => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? 3306,
        'dbname' => $_ENV['DB_DATABASE'],
        'user' => $_ENV['DB_USERNAME'],
        'password' => $_ENV['DB_PASSWORD'],

    ],

    'error_handling' => [

        'log_errors' => true,
        'display_error_details' => $boolean($_ENV['APP_DEBUG'] ?? 0),
        'log_error_details' => true

    ],

    'session' => [

        'name' => "{$formatAppName}_session",
        'flash_name' => "{$formatAppName}_flash",
        'secure' => $boolean($_ENV['SESSION_SECURE'] ?? true),
        'httponly' => $boolean($_ENV['SESSION_HTTP_ONLY'] ?? true),
        'samesite' => $_ENV['SESSION_SAME_SITE'] ?? 'lax',

    ],

    'mailer' => [

        'dsn' => $_ENV['MAILER_DSN'],
        'from' => $_ENV['MAILER_FROM'],

    ]

];
