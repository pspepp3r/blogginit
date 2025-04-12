<?php

declare(strict_types=1);

namespace Src\Middlewares;

use Slim\Views\Twig;
use Src\SessionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpSpecializedException;
use Psr\Http\Message\ResponseFactoryInterface;
use Src\Errors\LinkSignatureException;

class HttpSpecializedErrorMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Twig $twig,
    ) {}
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpSpecializedException $h) {
            $code = (string) $h->getCode();
            $message = $h->getMessage();

            return $this->responseFactory->createResponse(302)
            ->withHeader('Location', "/error?code=$code&message=$message");
        } catch (LinkSignatureException $l){
            $code = (string) $l->getCode();
            $message = $l->getMessage();

            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', "/error?code=$code&message=$message");
        }
    }
}
