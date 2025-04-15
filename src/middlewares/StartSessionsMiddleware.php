<?php

declare(strict_types=1);

namespace Src\Middlewares;

use Src\Services\ConfigService;
use Src\Services\RequestService;
use Src\Services\SessionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StartSessionsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ConfigService $config,
        private readonly RequestService $requestService,
        private readonly SessionInterface $session,
        private readonly SessionService $sessionService
    ) {}
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->session->isStarted()) {
            $this->session->setName($this->config->get('session.name'));
            $this->session->start();
        }

        $this->sessionService->storeSession(
            $request->getServerParams()['HTTP_USER_AGENT'],
            $request->getServerParams()['REMOTE_ADDR']
        );

        $response = $handler->handle($request);

        if ($request->getMethod() === 'GET' && ! $this->requestService->isXhr($request)) {
            $this->session->set('previousUrl', (string) $request->getUri());
        }
        $this->session->save();

        return $response;
    }
}
