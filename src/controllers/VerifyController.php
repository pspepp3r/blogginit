<?php

declare(strict_types = 1);

namespace Src\Controllers;

use Src\Providers\UserProvider;
use Src\Entities\User;
use Src\Mails\SignupEmail;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class VerifyController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly UserProvider $userProviderService,
        private readonly SignupEmail $signupEmail
    ) {
    }

    public function index(ResponseInterface $response): ResponseInterface
    {
        if ($this->twig->getEnvironment()->getGlobals()['user']?->getVerifiedAt()) {
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        return $this->twig->render($response, 'auth/verify.twig');
    }

    public function verify(ResponseInterface $response, array $args): ResponseInterface
    {
        /** @var User $user */
        $user = $this->twig->getEnvironment()->getGlobals()['user'];

        if (! hash_equals((string) $user->getId(), $args['id'])
            || ! hash_equals(sha1($user->getEmail()), $args['hash'])) {
            throw new \RuntimeException('Verification failed');
        }

        if (! $user->getVerifiedAt()) {
            $this->userProviderService->verifyUser($user);
        }

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function resend(ResponseInterface $response): ResponseInterface
    {
        $this->signupEmail->send($this->twig->getEnvironment()->getGlobals()['user']);

        return $response;
    }
}
