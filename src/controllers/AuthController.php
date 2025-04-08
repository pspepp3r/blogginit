<?php

declare(strict_types=1);

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;


class AuthController
{
    public function __construct(private Twig $twig) {}
    public function renderLogin(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'landing/login.twig', $args);
    }

    public function renderRegister(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'landing/register.twig', $args);
    }
}
