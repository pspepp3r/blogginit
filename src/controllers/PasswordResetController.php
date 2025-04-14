<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Src\Providers\UserProvider;
use Src\Mails\ForgotPasswordEmail;
use Src\Errors\ValidationException;
use Src\Services\PasswordResetService;
use Src\Validators\ResetPasswordRequestValidator;
use Src\Validators\ForgotPasswordRequestValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Src\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PasswordResetController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly UserProvider $userProvider,
        private readonly PasswordResetService $passwordResetService,
        private readonly ForgotPasswordEmail $forgotPasswordEmail
    ) {}

    public function renderForgotPasswordForm(Response $response): Response
    {
        return $this->twig->render($response, 'auth/forgot_password.twig');
    }

    public function handleForgotPasswordRequest(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(ForgotPasswordRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $user = $this->userProvider->getByCredentials($data);

        if ($user) {
            $this->passwordResetService->deactivateAllPasswordResets($data['email']);

            $passwordReset = $this->passwordResetService->generate($data['email']);

            $this->forgotPasswordEmail->send($passwordReset);
        }

        return $response;
    }

    public function renderResetPasswordForm(Response $response, array $args): Response
    {
        $passwordReset = $this->passwordResetService->getByToken($args['token']);

        if (! $passwordReset) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $this->twig->render($response, 'auth/reset_password.twig', ['token' => $args['token']]);
    }

    public function resetPassword(Request $request, Response $response, array $args): Response
    {
        $data = $this->requestValidatorFactory->make(ResetPasswordRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $passwordReset = $this->passwordResetService->getByToken($args['token']);

        if (! $passwordReset) {
            throw new ValidationException(['confirmPassword' => ['Invalid token']]);
        }

        $user = $this->userProvider->getByCredentials(['email' => $passwordReset->getEmail()]);

        if (! $user) {
            throw new ValidationException(['confirmPassword' => ['Invalid token']]);
        }

        $this->passwordResetService->updatePassword($user, $data['password']);

        return $response;
    }
}
