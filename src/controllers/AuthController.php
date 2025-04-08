<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Src\Services\AuthService;
use Src\Data_objects\RegisterUserData;
use Psr\Http\Message\ResponseInterface as Response;
use Src\Contracts\RequestValidatorFactoryInterface;
use Src\Validators\UserRegistrationRequestValidator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly SessionInterface $session,
        private Twig $twig
    ) {}
    public function renderLogin(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'landing/login.twig', $args);
    }

    public function renderRegister(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'landing/register.twig', $args);
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
        $data = $request->getParsedBody();

        return $response;
    }
}
