<?php

declare(strict_types=1);

namespace Src\Middlewares;

use Slim\Views\Twig;
use Slim\Routing\RouteContext;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Src\Services\AuthService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class NeutralMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly EntityManager $entityManager,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly SessionInterface $session,
        private readonly Twig $twig
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->auth->user();

        $this->twig->getEnvironment()->addGlobal('user', $user);
        $this->twig->getEnvironment()->addGlobal(
            'current_route',
            RouteContext::fromRequest($request)->getRoute()->getName()
        );

        return $handler->handle($request);
    }
}
