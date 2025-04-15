<?php

declare(strict_types=1);

namespace Src\Middlewares;

use Src\Providers\BlogProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class ViewsIncrement implements MiddlewareInterface
{
    public function __construct(
        private readonly BlogProvider $blogProvider,
        private readonly ResponseFactoryInterface $responseFactory
    ) {}
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (str_contains($request->getServerParams()['REQUEST_URI'], '/blog/')) {
            $uuid = substr($request->getServerParams()['REQUEST_URI'], 6);
            $response = $this->responseFactory->createResponse(302);

            if ($blog = $this->blogProvider->getByUUId($uuid)) {
                $blog->incrementViews();
                $this->blogProvider->sync($blog);

            } else if ($uuid == 'profile') {
                return $response->withHeader('Location', '/blogs/profile');
            } else {
                return $response->withHeader('Location', '/error?code=404&message=Blog removed or not existing');
            }
        }

        return $handler->handle($request);
    }
}
