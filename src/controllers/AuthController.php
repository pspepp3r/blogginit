<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Src\Services\AuthService;
use Src\Enums\AuthAttemptStatus;
use Src\Errors\ValidationException;
use Src\Data_objects\RegisterUserData;
use Src\Validators\UserLoginRequestValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Src\Contracts\RequestValidatorFactoryInterface;
use Src\Validators\UserRegistrationRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Src\Services\ResponseFormatterService;
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

    public function register(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserRegistrationRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->authService->register(new RegisterUserData($data['name'], $data['email'], $data['password']));

        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $status = $this->authService->attemptLogin($data);
        
        if ($status === AuthAttemptStatus::FAILED) {
            throw new ValidationException(['password' => ['You have entered an invalid username or password']]);
        }

        if ($status === AuthAttemptStatus::TWO_FACTOR_AUTH) {
            return $this->responseFormatter->asJson($response, ['two_factor' => true]);
        }

        // return $this->responseFormatter->asJson($response, []);
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    public function logOut(Response $response): Response
    {
        $this->authService->logOut();

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
