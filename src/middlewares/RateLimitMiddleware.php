<?php

declare(strict_types = 1);

namespace Src\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Src\Services\ConfigService;
use Src\Services\RequestService;

class RateLimitMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly RequestService $requestService,
        private readonly ConfigService $config,
        // private readonly RateLimiterFactory $rateLimiterFactory
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // $clientIp     = $this->requestService->getClientIp($request, $this->config->get('trusted_proxies'));
        // $routeContext = RouteContext::fromRequest($request);
        // $route        = $routeContext->getRoute();
        // $limiter      = $this->rateLimiterFactory->create($route->getName() . '_' . $clientIp);

        // if ($limiter->consume()->isAccepted() === false) {
        //     return $this->responseFactory->createResponse(429, 'Too many requests');
        // }

        return $handler->handle($request);
    }
}
