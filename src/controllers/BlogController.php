<?php

declare(strict_types=1);

namespace Src\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;


class BlogController
{
    public function __construct(private Twig $twig) {}
    public function renderReports(Response $response, array $args): Response
    {
        return $this->twig->render($response, 'app/reports.twig', $args);
    }
}
