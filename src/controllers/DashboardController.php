<?php

declare(strict_types=1);

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;


class DashboardController
{
    public function __construct(private Twig $twig) {}
    public function index(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'app/dashboard.twig', $args);
    }
}
