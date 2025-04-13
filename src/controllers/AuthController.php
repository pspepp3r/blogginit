<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Src\Services\AuthService;
use Src\Enums\AuthAttemptStatus;
use Src\Errors\ValidationException;
use Src\Data_objects\RegisterUserData;
use Src\Services\ResponseFormatterService;
use Src\Validators\UserLoginRequestValidator;
use Src\Validators\TwoFactorLoginRequestValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Src\Contracts\RequestValidatorFactoryInterface;
use Src\Validators\UserRegistrationRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly ResponseFormatterService $responseFormatter,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly SessionInterface $session,
        private Twig $twig
    ) {}
    public function renderLogin(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'auth/login.twig', $args);
    }

    public function renderRegister(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'auth/register.twig', $args);
    }

    public function renderTwoFactorLoginForm(Response $response): Response
    {
        return $this->twig->render($response, 'auth/2fa.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserRegistrationRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->authService->register(
            new RegisterUserData($data['name'], $data['email'], $data['password']),
            $request
        );

        return $this->responseFormatter->asJson($response, []);
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $status = $this->authService->attemptLogin($data, $request);

        if ($status === AuthAttemptStatus::FAILED) {
            throw new ValidationException(['email' => ['You have entered an invalid username or password']]);
        }

        if ($status === AuthAttemptStatus::TWO_FACTOR_AUTH) {
            return $this->responseFormatter->asJson($response, ['two_factor' => true]);
        }

        return $this->responseFormatter->asJson($response, []);
    }

    public function twoFactorLogin(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(TwoFactorLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        if (! $this->authService->attemptTwoFactorLogin($data, $request)) {
            throw new ValidationException(['code' => ['Invalid Code']]);
        }

        return $response;
    }

    public function logOut(Response $response): Response
    {
        $this->authService->logOut();

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
