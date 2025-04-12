<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Src\Services\SettingsService;
use Src\Validators\UpdateProfileRequestValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Src\Contracts\RequestValidatorFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SettingsController
{
    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly SettingsService $settingsService,
        private Twig $twig
    ) {}
    public function index(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'app/settings.twig', $args);
    }

    public function handleProfileSettings(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UpdateProfileRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->settingsService->updateProfile($data['name'], $data['email']);

        return $response;
    }

    public function handleSecuritySettings(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $this->settingsService->updateSecurity($data['password'], $data['2faEnable']);

        return $response;
    }
}
