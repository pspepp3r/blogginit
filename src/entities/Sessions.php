<?php

declare(strict_types=1);

namespace Src\Entities;

use DateTime;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity, Table('sessions')]
class Sessions
{
    #[Id, Column]
    private string $id;

    #[Column('ip_address')]
    private string $ipAddress;

    #[Column('user_agent')]
    private string $userAgent;

    #[Column('last_action')]
    private DateTime $lastAction;

    #[ManyToOne(cascade: ['remove'])]
    private User $user;

    public function getId(): string
    {
        return $this->id;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): static
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): static
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getLastAction(): DateTime
    {
        return $this->lastAction;
    }

    public function setLastAction(DateTime $lastAction): static
    {
        $this->lastAction = $lastAction;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
