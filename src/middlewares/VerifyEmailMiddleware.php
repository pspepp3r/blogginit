<?php

declare(strict_types = 1);

namespace Src\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Src\Entities\User;

class VerifyEmailMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var User
         */
        $user = $request->getAttribute('user');

        if ($user->getVerifiedAt()) {
            return $handler->handle($request);
        }

        return $this->responseFactory->createResponse(302)->withHeader('Location', '/verify');
    }
}
