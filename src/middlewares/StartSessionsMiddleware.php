<?php

declare(strict_types=1);

namespace Src\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Src\Services\RequestService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StartSessionsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SessionInterface $session,
        private readonly RequestService $requestService
    ) {}
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->session->start();

        $response = $handler->handle($request);

        if ($request->getMethod() === 'GET' && ! $this->requestService->isXhr($request)) {
            $this->session->set('previousUrl', (string) $request->getUri());
        }

        // $this->session->save();

        return $response;
    }
}
