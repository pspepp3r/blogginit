<?php

declare(strict_types=1);

namespace Src\Services;

use Psr\Http\Message\ServerRequestInterface;
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
        private readonly SessionService $sessionService,
        private readonly SignupEmail $signupEmail,
        private readonly UserProvider $userProvider,
        private readonly UserLoginCodeService $userLoginCodeService
    ) {}

    public function checkCredentials(User $user, array $credentials): bool
    {
        return password_verify($credentials['password'], $user->getPassword());
    }

    public function register(RegisterUserData $data, $request): User
    {
        $user = $this->userProvider->createUser($data);

        $this->logIn($user, $request);

        $this->signupEmail->send($user);

        return $user;
    }

    public function logIn(User $user, ServerRequestInterface $request): void
    {
        $this->session->migrate(true);
        $this->session->set('user', $user->getId());
        $this->session->set('userEntity', $user);
        $this->sessionService->storeSession(
            $request->getServerParams()['HTTP_USER_AGENT'],
            $request->getServerParams()['REMOTE_ADDR']
        );

        $this->user = $user;
    }

    public function startLoginWith2FA(User $user): void
    {
        $this->session->migrate(true);
        $this->session->set('2fa', $user->getId());

        $this->userLoginCodeService->deactivateAllActiveCodes($user);

        $this->twoFactorAuthEmail->send($this->userLoginCodeService->generate($user));
    }

    public function attemptLogin(array $credentials, $request): AuthAttemptStatus
    {
        $user = $this->userProvider->getByCredentials($credentials);

        if (! $user || ! $this->checkCredentials($user, $credentials)) {
            return AuthAttemptStatus::FAILED;
        }

        if ($user->hasTwoFactorAuthEnabled()) {
            $this->startLoginWith2FA($user);

            return AuthAttemptStatus::TWO_FACTOR_AUTH;
        }

        $this->logIn($user, $request);

        return AuthAttemptStatus::SUCCESS;
    }

    public function attemptTwoFactorLogin(array $data, $request): bool
    {
        $userId = $this->session->get('2fa');

        if (! $userId) {
            return false;
        }

        $user = $this->userProvider->getById($userId);

        if (! $user || $user->getEmail() !== $data['email']) {
            return false;
        }

        if (! $this->userLoginCodeService->verify($user, $data['code'])) {
            return false;
        }

        $this->session->remove('2fa');

        $this->logIn($user, $request);

        $this->userLoginCodeService->deactivateAllActiveCodes($user);

        return true;
    }

    public function logOut(): void
    {
        $this->session->remove('user');
        $this->session->remove('userEntity');
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
