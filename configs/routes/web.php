<?php

declare(strict_types=1);

use Slim\App;
use Src\Controllers\AuthController;
use Src\Controllers\LandingController;

return function (App $app): void {

    // Homepage
    $app->get('/', [LandingController::class, 'index']);
    $app->get('/404', [LandingController::class, '_404']);

    $app->get('/login', [AuthController::class, 'renderLogin']);
    $app->get('/register', [AuthController::class, 'renderRegister']);
};
