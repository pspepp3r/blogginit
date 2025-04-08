<?php

declare(strict_types=1);

namespace Src\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Src\SessionService;

class LandingController
{
    public function __construct(private Twig $twig, private readonly SessionService $session) {}
    public function index(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'landing/landing.twig', $args);
    }

    public function error(Response $response, array $args): Response
    {
        $args = [
        'code' => $this->session->get('code'),
        'message' => $this->session->get('message')
        ];

        return $this->twig->render($response, 'landing/error.twig', $args);
    }
}
