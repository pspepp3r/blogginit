<?php

declare(strict_types=1);

namespace Src\Providers;

use Src\Entities\User;
use Src\Services\HashService;
use Doctrine\ORM\EntityManager;
use Src\Data_objects\RegisterUserData;

class UserProvider
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly HashService $hashService
    ) {}

    public function createUser(RegisterUserData $data): User
    {
        $user = new User();

        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setPassword($this->hashService->hashPassword($data->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function verifyUser(User $user): void
    {
        $user->setVerifiedAt(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function getById(int $userId): ?User
    {
        return $this->entityManager->find(User::class, $userId);
    }

    public function getByCredentials(array $credentials): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
    }
}
