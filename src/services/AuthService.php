<?php

declare(strict_types=1);

namespace Src\Services;

use Src\Entities\User;
use Src\Mails\SignupEmail;
use Src\Providers\UserProvider;
use Src\Enums\AuthAttemptStatus;
use Src\Data_objects\RegisterUserData;
use Src\Mails\TwoFactorAuthEmail;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthService
{
    private ?User $user = null;

    public function __construct(
        private readonly TwoFactorAuthEmail $twoFactorAuthEmail,
        private readonly SessionInterface $session,
        private readonly SignupEmail $signupEmail,
        private readonly UserProvider $userProvider,
        private readonly UserLoginCodeService $userLoginCodeService
    ) {}

    public function checkCredentials(User $user, array $credentials): bool
    {
        return password_verify($credentials['password'], $user->getPassword());
    }

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

    public function startLoginWith2FA(User $user): void
    {
        $this->session->migrate(true);
        $this->session->set('2fa', $user->getId());

        $this->userLoginCodeService->deactivateAllActiveCodes($user);

        $this->twoFactorAuthEmail->send($this->userLoginCodeService->generate($user));
    }

    public function attemptLogin(array $credentials): AuthAttemptStatus
    {
        $user = $this->userProvider->getByCredentials($credentials);

        if (! $user || ! $this->checkCredentials($user, $credentials)) {
            return AuthAttemptStatus::FAILED;
        }

        if ($user->hasTwoFactorAuthEnabled()) {
            $this->startLoginWith2FA($user);

            return AuthAttemptStatus::TWO_FACTOR_AUTH;
        }

        $this->logIn($user);

        return AuthAttemptStatus::SUCCESS;
    }

    public function logOut(): void
    {
        $this->session->remove('user');
        $this->session->migrate(true);

        $this->user = null;
    }

    public function user(): ?User
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $userId = $this->session->get('user');

        if (! $userId) {
            return null;
        }

        $user = $this->userProvider->getById($userId);

        if (! $user) {
            return null;
        }

        $this->user = $user;

        return $this->user;
    }
}
