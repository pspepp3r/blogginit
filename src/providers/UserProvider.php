<?php

declare(strict_types=1);

namespace Src\Providers;

use Src\Entities\Blog;
use Src\Entities\User;
use Src\Services\HashService;
use Doctrine\ORM\EntityManager;
use Src\Data_objects\RegisterUserData;
use Src\Entities\Interactions;

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
        $user->setPicture('default.png');

        $this->sync($user);

        return $user;
    }

    public function verifyUser(User $user): void
    {
        $user->setVerifiedAt(new \DateTime());

        $this->sync($user);
    }

    public function getById(int $userId): ?User
    {
        return $this->entityManager->find(User::class, $userId);
    }

    public function getByCredentials(array $credentials): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->setPassword($this->hashService->hashPassword($password));

        $this->sync($user);
    }

    public function ticked(User|string $user, Blog $blog): bool
    {
        if ($user instanceof User) {
            return $this->entityManager
                ->getRepository(Interactions::class)
                ->findOneBy(['user' => $user, 'blog' => $blog]) ? true : false;
        } else {
            return false;
        }
    }

    public function sync(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
