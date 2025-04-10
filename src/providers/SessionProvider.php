<?php

declare(strict_types=1);

namespace Src\Providers;

use DateTime;
use Src\Entities\User;
use Src\Entities\Sessions;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionProvider
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly SessionInterface $session
    ) {}
    public function findById(string $id): Sessions|null
    {
        return $this->entityManager->find(Sessions::class, $id);
    }

    public function findByUser(int $user): Sessions|null
    {
        return $this->entityManager->getRepository(Sessions::class)
            ->findOneBy(['user_id' => $user]);
    }

    public function store(Sessions $session, string $sessionId, $userAgent, $IpAddress)
    {
        $session
            ->setId($sessionId)
            ->setIpAddress($IpAddress)
            ->setUserAgent($userAgent)
            ->setLastAction(new DateTime());

        if ($user = $this->session->get('userEntity')) {
            $session->setUser($user);
        }

        $this->entityManager->persist($session);
        $this->entityManager->flush();
    }

    public function update(Sessions $session, DateTime $dateTime)
    {
        $session->setLastAction($dateTime);

        $this->entityManager->persist($session);
        $this->entityManager->flush();
    }
}
