<?php

declare(strict_types=1);

namespace Src\Services;

use DateTime;
use Slim\Views\Twig;
use Src\Entities\Sessions;
use Src\Entities\User;
use Src\Providers\SessionProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionService
{
    public function __construct(
        private readonly SessionInterface $session,
        private readonly SessionProvider $sessionProvider,
    ) {}

    public function storeSession(string $userAgent, string $IpAddress)
    {
        $session = $this->sessionProvider->getById(
            $this->session->getId()
        );

        if (! $session) {
            $session = new Sessions();

            $this->sessionProvider->store(
                $session,
                $this->session->getId(),
                $userAgent,
                $IpAddress
            );
        } else {
            $this->sessionProvider->update($session, new DateTime());
        }
    }

    public function getSession(User $user): Sessions|null
    {
        return $this->sessionProvider->getByUser($user->getId());
    }
}
