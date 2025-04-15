<?php

declare(strict_types=1);

use Slim\App;
use Src\Controllers\AuthController;
use Src\Controllers\BlogsController;
use Src\Middlewares\AuthMiddleware;
use Src\Middlewares\GuestMiddleware;
use Slim\Routing\RouteCollectorProxy;
use Src\Controllers\ExtrasController;
use Src\Controllers\VerifyController;
use Src\Controllers\LandingController;
use Src\Controllers\ReportsController;
use Src\Middlewares\NeutralMiddleware;
use Src\Controllers\SettingsController;
use Src\Controllers\DashboardController;
use Src\Middlewares\VerifyEmailMiddleware;
use Src\Controllers\PasswordResetController;
use Src\Controllers\CollaborationsController;
use Src\Middlewares\ValidateSignatureMiddleware;

return function (App $app): void {
    // Homepage

    $app->get('/error', [LandingController::class, 'error']);

    $app->group('/extra', function (RouteCollectorProxy $extra) {
        $extra->get('/about', [ExtrasController::class, 'renderAbout']);
        $extra->get('/help', [ExtrasController::class, 'renderHelp']);
        $extra->get('/privacy', [ExtrasController::class, 'renderPrivacy']);
        $extra->get('/services', [ExtrasController::class, 'renderServiceTerms']);
    })->add(NeutralMiddleware::class);


    $app->group('/blog', function (RouteCollectorProxy $blogs) {
        $blogs->get('/profile', [BlogsController::class, '']);
        $blogs->get('/{uuid}', [BlogsController::class, 'renderBlog']);
    })->add(NeutralMiddleware::class);

    $app->group('', function (RouteCollectorProxy $guest) {
        $guest->get('/', [LandingController::class, 'index']);
        $guest->group('', function (RouteCollectorProxy $auth) {
            $auth->get('/login', [AuthController::class, 'renderLogin']);
            $auth->get('/register', [AuthController::class, 'renderRegister']);
            $auth->get('/forgot-password', [PasswordResetController::class, 'renderForgotPasswordForm']);
            $auth->get('/reset-password/{token}', [PasswordResetController::class, 'renderResetPasswordForm'])
                ->setName('password-reset')
                ->add(ValidateSignatureMiddleware::class);
            $auth->get('/two-factor-form', [AuthController::class, 'renderTwoFactorLoginForm']);

            $auth->post('/register', [AuthController::class, 'register']);
            $auth->group('/login', function (RouteCollectorProxy $login) {
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

        $main->group('/blogs', function (RouteCollectorProxy $blogs) {
            $blogs->get('', [BlogsController::class, 'renderBlogs']);
            $blogs->get('/create', [BlogsController::class, 'renderCreateBlog']);
            $blogs->get('/profile', [BlogsController::class, '']);
            $blogs->get('/edit/{}', [BlogsController::class, '']);
            $blogs->get('/view/{}', [BlogsController::class, '']);

            $blogs->post('/create', [BlogsController::class, '']);
        });

        $main->group('/reports', function (RouteCollectorProxy $reports) {
            $reports->get('', [ReportsController::class, 'renderReports']);
            $reports->get('/{}', [ReportsController::class, '']);
        });

        $main->group('/settings', function (RouteCollectorProxy $settings) {
            $settings->get('', [SettingsController::class, 'index']);

            $settings->post('/update-profile-settings', [SettingsController::class, 'handleProfileSettings']);
            $settings->post('/update-security-settings', [SettingsController::class, 'handleSecuritySettings']);
        });

        $main->post('/logout', [AuthController::class, 'logOut']);
    })->add(VerifyEmailMiddleware::class)->add(AuthMiddleware::class);
};
