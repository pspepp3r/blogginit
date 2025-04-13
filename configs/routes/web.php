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
use Src\Controllers\AbstractController;
use Src\Controllers\SettingsController;
use Src\Controllers\DashboardController;
use Src\Middlewares\VerifyEmailMiddleware;
use Src\Controllers\PasswordResetController;
use Src\Controllers\CollaborationsController;
use Src\Middlewares\ValidateSignatureMiddleware;

return function (App $app): void {
    $app->get('/error', [LandingController::class, 'error']);

    $app->group('', function (RouteCollectorProxy $guest) {
        $guest->get('/', [LandingController::class, 'index']);

        $guest->group('', function (RouteCollectorProxy $auth) {
            $auth->get('/register', [AuthController::class, 'renderRegister']);
            $auth->get('/forgot-password', [PasswordResetController::class, 'renderForgotPasswordForm']);
            $auth->get('/reset-password/{token}', [PasswordResetController::class, 'renderResetPasswordForm'])
            ->setName('password-reset')
            ->add(ValidateSignatureMiddleware::class);
            $auth->get('/two-factor-form', [AuthController::class, 'renderTwoFactorLoginForm']);

            $auth->post('/register', [AuthController::class, 'register']);

            $auth->group('/login', function (RouteCollectorProxy $login) {
                $login->get('', [AuthController::class, 'renderLogin']);
                
                $login->post('', [AuthController::class, 'login']);
                $login->post('/two-factor', [AuthController::class, 'twoFactorLogin'])
                    ->setName('twoFactorLogin');
                // ->add(RateLimitMiddleware::class);
            });

            $auth->post('/forgot-password', [PasswordResetController::class, 'handleForgotPasswordRequest'])
                ->setName('handleForgotPassword');
            // ->add(RateLimitMiddleware::class);
            $auth->post('/reset-password/{token}', [PasswordResetController::class, 'resetPassword'])
                ->setName('resetPassword');
            // ->add(RateLimitMiddleware::class);
        });
    })->add(GuestMiddleware::class);

    $app->group('/verify', function (RouteCollectorProxy $mail) {
        $mail->get('', [VerifyController::class, 'index']);
        $mail->get('/{id}/{hash}', [VerifyController::class, 'verify'])
            ->setName('verify')
            ->add(ValidateSignatureMiddleware::class);
        $mail->post('', [VerifyController::class, 'resend'])
            ->setName('resendVerification');
        // ->add(RateLimitMiddleware::class);
    })->add(AuthMiddleware::class);


    $app->group('', function (RouteCollectorProxy $main) {
        $main->get('/collaborations', [CollaborationsController::class, 'index']);
        $main->get('/dashboard', [DashboardController::class, 'index']);
        $main->get('/profile', [DashboardController::class, 'renderProfile']);
        $main->get('/reports', [BlogController::class, 'renderReports']);

        $main->group('/settings', function (RouteCollectorProxy $settings) {
            $settings->get('', [SettingsController::class, 'index']);
            $settings->post('/update-profile-settings', [SettingsController::class, 'handleProfileSettings']);
            $settings->post('/update-security-settings', [SettingsController::class, 'handleSecuritySettings']);
        });

        $main->group('/abstract', function (RouteCollectorProxy $abstract) {
            $abstract->get('/about', [AbstractController::class, 'renderAbout']);
            $abstract->get('/help', [AbstractController::class, 'renderHelp']);
            $abstract->get('/privacy', [AbstractController::class, 'renderPrivacy']);
            $abstract->get('/tos', [AbstractController::class, 'renderServiceTerms']);
        });


        $main->post('/logout', [AuthController::class, 'logOut']);
    })->add(VerifyEmailMiddleware::class)->add(AuthMiddleware::class);
};
