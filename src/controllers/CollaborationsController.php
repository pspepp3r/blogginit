<?php

declare(strict_types=1);

namespace Src\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Src\Contracts\RequestValidatorFactoryInterface;
use Src\Validators\UpdateProfileRequestValidator;

class CollaborationsController
{
    public function __construct(
        private Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory
    ) {}
    public function index(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'app/collaborations.twig', $args);
    }

    public function handleProfileSettings(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UpdateProfileRequestValidator::class)->validate(
            $request->getParsedBody()
        );



        return $response;
    }
}
