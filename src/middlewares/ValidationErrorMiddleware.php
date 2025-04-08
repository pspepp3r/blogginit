<?php

declare(strict_types=1);

namespace Src\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ValidationErrorMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Twig $twig,
        private readonly SessionInterface $session
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($errors = $this->session->get('flash:errors')) {
            $this->twig->getEnvironment()->addGlobal('errors', $errors);
            $this->session->remove('flash:errors');
        }

        if ($old = $this->session->get('flash:old')) {
            $this->twig->getEnvironment()->addGlobal('old', $old);
            $this->session->remove('flash:old');
        }

        return $handler->handle($request);
    }
}
