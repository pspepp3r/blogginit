<?php

declare(strict_types=1);

namespace Src\Services;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Src\Entities\PasswordReset;
use Src\Entities\User;
use Src\Providers\UserProvider;

class PasswordResetService
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly UserProvider $userProvider
    ) {}

    public function generate(string $email): PasswordReset
    {
        $passwordReset = new PasswordReset();

        $passwordReset->setToken(bin2hex(random_bytes(32)));
        $passwordReset->setExpiration(new \DateTime('+30 minutes'));
        $passwordReset->setEmail($email);

        $this->entityManager->persist($passwordReset);
        $this->entityManager->flush();

        return $passwordReset;
    }

    public function deactivateAllPasswordResets(string $email): void
    {
        $this->entityManager
            ->getRepository(PasswordReset::class)
            ->createQueryBuilder('pr')
            ->update()
            ->set('pr.isActive', '0')
            ->where('pr.email = :email')
            ->andWhere('pr.isActive = 1')
            ->setParameter('email', $email)
            ->getQuery()
            ->execute();
    }

    public function getByToken(string $token): ?PasswordReset
    {
        return $this->entityManager
            ->getRepository(PasswordReset::class)
            ->createQueryBuilder('pr')
            ->select('pr')
            ->where('pr.token = :token')
            ->andWhere('pr.isActive = :active')
            ->andWhere('pr.expiration > :now')
            ->setParameter('token', $token)
            ->setParameter('active', true)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updatePassword(User $user, string $password): void
    {
        $this->entityManager->wrapInTransaction(function () use ($user, $password) {
            $this->deactivateAllPasswordResets($user->getEmail());

            $this->userProvider->updatePassword($user, $password);
        });
    }
}
