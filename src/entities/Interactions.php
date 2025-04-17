<?php

declare(strict_types=1);

namespace Src\Entities;

use Src\Entities\Blog;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Src\Entities\Traits\HasTimestamps;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Src\Enums\Interactions as EnumsInteractions;

#[Entity, Table('interactions')]
#[HasLifecycleCallbacks]
class Interactions
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column('ip_address', nullable: true)]
    private ?string $ipAddress;

    #[Column(enumType: EnumsInteractions::class)]
    private EnumsInteractions $interaction;

    #[ManyToOne(inversedBy: 'interactions')]
    private ?User $user;

    #[ManyToOne(inversedBy: 'interactions')]
    private Blog $blog;

    public function __construct() {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): static
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getInteraction(): EnumsInteractions
    {
        return $this->interaction;
    }

    public function setInteraction(EnumsInteractions $interaction): static
    {
        $this->interaction = $interaction;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }
    
    public function setUser(User $user): static
    {
        $this->user = $user;
        $user->addInteraction($this);

        return $this;
    }
    
    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(Blog $blog): static
    {
        $this->blog = $blog;
        $blog->addInteraction($this);

        return $this;
    }
}
