<?php

declare(strict_types=1);

namespace Src\Services;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RequestService
{
    public function __construct(private readonly SessionInterface $session) {}

    public function getReferer(ServerRequestInterface $request): string
    {
        $referer = $request->getHeader('referer')[0] ?? '';

        if (! $referer) {
            return $this->session->get('previousUrl');
        }

        $refererHost = parse_url($referer, PHP_URL_HOST);

        if ($refererHost !== $request->getUri()->getHost()) {
            $referer = $this->session->get('previousUrl');
        }

        return $referer;
    }

    public function isXhr(ServerRequestInterface $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    public function getClientIp(ServerRequestInterface $request, array $trustedProxies): ?string
    {
        $serverParams = $request->getServerParams();

        if (
            in_array($serverParams['REMOTE_ADDR'], $trustedProxies, true)
            && isset($serverParams['HTTP_X_FORWARDED_FOR'])
        ) {
            $ips = explode(',', $serverParams['HTTP_X_FORWARDED_FOR']);

            return trim($ips[0]);
        }

        return $serverParams['REMOTE_ADDR'] ?? null;
    }
}
