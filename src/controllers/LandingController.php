<?php

declare(strict_types=1);

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;


class LandingController
{
    public function __construct(private Twig $twig) {}
    public function index(Response $response, array $args): Response
    {
        $args = [
            'title' => 'Prophetic | Home',
        ];
        return $this->twig->render($response, 'landing/landing.twig', $args);
    }

    public function _404(Response $response, array $args): Response
    {
        $args = [
            'title' => 'Prophetic | 404',
        ];
        return $this->twig->render($response, 'landing/_404.twig', $args);
    }
}
