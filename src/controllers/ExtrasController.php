<?php

declare(strict_types=1);

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;


class ExtrasController
{
    public function __construct(private Twig $twig) {}

    public function renderAbout(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'extras/about.twig', $args);
    }

    public function renderHelp(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'app/help.twig', $args);
    }

    public function renderPrivacy(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'app/privacy.twig', $args);
    }

    public function renderServiceTerms(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'app/services.twig', $args);
    }
}
