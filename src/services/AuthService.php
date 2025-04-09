<?php

declare(strict_types=1);

namespace Src\Services;

use Src\Entities\User;
use Src\Data_objects\RegisterUserData;
use Src\Mails\SignupEmail;
use Src\Providers\UserProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthService
{
    private ?User $user = null;

    public function __construct(
        private readonly SessionInterface $session,
        private readonly SignupEmail $signupEmail,
        private readonly UserProvider $userProvider
    ) {}

    public function register(RegisterUserData $data): User
    {
        $user = $this->userProvider->createUser($data);

        $this->logIn($user);

        $this->signupEmail->send($user);

        return $user;
    }

    public function logIn(User $user): void
    {
        $this->session->migrate(true);
        $this->session->set('user', $user->getId());

        $this->user = $user;
    }

    public function logOut(): void
    {
        $this->session->remove('user');
        $this->session->migrate(true);

        $this->user = null;
    }
}
