<?php

declare(strict_types=1);

use Slim\App;
use Src\Controllers\AuthController;
use Src\Controllers\BlogController;
use Src\Middlewares\AuthMiddleware;
use Src\Middlewares\GuestMiddleware;
use Slim\Routing\RouteCollectorProxy;
use Src\Controllers\VerifyController;
use Src\Controllers\LandingController;
use Src\Controllers\DashboardController;
use Src\Middlewares\VerifyEmailMiddleware;
use Src\Middlewares\ValidateSignatureMiddleware;

return function (App $app): void {
    // Homepage

    $app->get('/error', [LandingController::class, 'error']);

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/', [LandingController::class, 'index']);
        $group->get('/login', [AuthController::class, 'renderLogin']);
        $group->get('/register', [AuthController::class, 'renderRegister']);

        $group->post('/login', [AuthController::class, 'login']);
        $group->post('/register', [AuthController::class, 'register']);
    })->add(GuestMiddleware::class);

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/logout', [AuthController::class, 'logOut']);
        $group->get('/verify', [VerifyController::class, 'index']);
        $group->get('/verify/{id}/{hash}', [VerifyController::class, 'verify'])
            ->setName('verify')
            ->add(ValidateSignatureMiddleware::class);
        $group->post('/verify', [VerifyController::class, 'resend'])
            ->setName('resendVerification');
            // ->add(RateLimitMiddleware::class);
    })->add(AuthMiddleware::class);

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/dashboard', [DashboardController::class, 'index']);
        $group->get('/reports', [BlogController::class, 'renderReports']);
    })->add(VerifyEmailMiddleware::class)->add(AuthMiddleware::class);
};
