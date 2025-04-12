<?php

declare(strict_types=1);

namespace Src\Services;

use Doctrine\ORM\EntityManager;
use Src\Entities\User;
use Src\Providers\UserProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SettingsService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly SessionInterface $sessionInterface,
        private readonly UserProvider $userProvider
    ) {}
    public function updateProfile(?string $name, ?string $email): void
    {
        /**
         * @var int
         */
        $userId = $this->sessionInterface->get('user');

        $user = $this->userProvider->getById($userId);


        if ($name) {
            $user->setName($name);
        }
        if ($email) {
            $user->setEmail($email);
        }

        $this->sessionInterface->set('userEntity', $user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
