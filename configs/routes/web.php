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
use Src\Controllers\PasswordResetController;
use Src\Middlewares\ValidateSignatureMiddleware;

return function (App $app): void {
    // Homepage

    $app->get('/error', [LandingController::class, 'error']);

    $app->group('', function (RouteCollectorProxy $guest) {
        $guest->get('/', [LandingController::class, 'index']);
        $guest->get('/login', [AuthController::class, 'renderLogin']);
        $guest->get('/register', [AuthController::class, 'renderRegister']);
        $guest->get('/forgot-password', [PasswordResetController::class, 'renderForgotPasswordForm']);
        $guest->get('/reset-password/{token}', [PasswordResetController::class, 'renderResetPasswordForm'])
            ->setName('password-reset')
            ->add(ValidateSignatureMiddleware::class);

        $guest->post('/login', [AuthController::class, 'login']);
        $guest->post('/register', [AuthController::class, 'register']);
        $guest->post('/login/two-factor', [AuthController::class, 'twoFactorLogin'])
            ->setName('twoFactorLogin');
            // ->add(RateLimitMiddleware::class);
        $guest->post('/forgot-password', [PasswordResetController::class, 'handleForgotPasswordRequest'])
            ->setName('handleForgotPassword');
            // ->add(RateLimitMiddleware::class);
        $guest->post('/reset-password/{token}', [PasswordResetController::class, 'resetPassword'])
            ->setName('resetPassword');
            // ->add(RateLimitMiddleware::class);
    })->add(GuestMiddleware::class);

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/verify', [VerifyController::class, 'index']);
        $group->get('/verify/{id}/{hash}', [VerifyController::class, 'verify'])
        ->setName('verify')
        ->add(ValidateSignatureMiddleware::class);
        $group->post('/logout', [AuthController::class, 'logOut']);
        $group->post('/verify', [VerifyController::class, 'resend'])
            ->setName('resendVerification');
            // ->add(RateLimitMiddleware::class);
    })->add(AuthMiddleware::class);

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/dashboard', [DashboardController::class, 'index']);
        $group->get('/reports', [BlogController::class, 'renderReports']);
    })->add(VerifyEmailMiddleware::class)->add(AuthMiddleware::class);
};
