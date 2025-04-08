<?php

declare(strict_types=1);

use Slim\App;
use Src\Controllers\AuthController;
use Src\Controllers\BlogController;
use Src\Controllers\DashboardController;
use Src\Controllers\LandingController;

return function (App $app): void {

    // Homepage
    $app->get('/', [LandingController::class, 'index']);
    $app->get('/error', [LandingController::class, 'error']);

    $app->get('/login', [AuthController::class, 'renderLogin']);
    $app->get('/register', [AuthController::class, 'renderRegister']);

    $app->get('/dashboard', [DashboardController::class, 'index']);
    $app->get('/reports', [BlogController::class, 'renderReports']);
};
