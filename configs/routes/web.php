<?php

declare(strict_types=1);

use Slim\App;
use Src\Controllers\AuthController;
use Src\Controllers\BlogController;
use Slim\Routing\RouteCollectorProxy;
use Src\Controllers\LandingController;
use Src\Controllers\DashboardController;

return function (App $app): void {
    // Homepage
    $app->get('/error', [LandingController::class, 'error']);

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/', [LandingController::class, 'index']);
        $group->get('/login', [AuthController::class, 'renderLogin']);
        $group->get('/register', [AuthController::class, 'renderRegister']);

        $group->post('/login', [AuthController::class, 'login']);
        $group->post('/register', [AuthController::class, 'register']);
    });

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/dashboard', [DashboardController::class, 'index']);
        $group->get('/reports', [BlogController::class, 'renderReports']);
    });
};
