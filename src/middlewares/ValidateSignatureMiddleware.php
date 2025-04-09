<?php

declare(strict_types = 1);

namespace Src\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Src\Services\ConfigService;

class ValidateSignatureMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ConfigService $config)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri               = $request->getUri();
        $queryParams       = $request->getQueryParams();
        $originalSignature = $queryParams['signature'] ?? '';
        $expiration        = (int) ($queryParams['expiration'] ?? 0);

        unset($queryParams['signature']);

        $url       = (string) $uri->withQuery(http_build_query($queryParams));
        $signature = hash_hmac('sha256', $url, $this->config->get('app.app_key'));

        if ($expiration <= time() || ! hash_equals($signature, $originalSignature)) {
            throw new \RuntimeException('Failed to verify signature');
        }

        return $handler->handle($request);
    }
}
