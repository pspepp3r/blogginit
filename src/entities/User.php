<?php

declare(strict_types=1);

namespace Src\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[Entity, Table('users')]
#[HasLifecycleCallbacks]
class User
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[Column]
    private string $email;

    #[Column]
    private string $password;

    #[Column(nullable: true)]
    private string $picture;

    #[Column('two_factor',  options: ['default' => false])]
    private bool $twoFactor;

    #[Column('verified_at', nullable: true)]
    private ?DateTime $verifiedAt;

    #[Column('joined_at')]
    private DateTime $joinedAt;

    #[Column('updated_at')]
    private DateTime $updatedAt;

    #[OneToMany(mappedBy: 'user', targetEntity: Blog::class)]
    private Collection $blogs;

    public function __construct() {
        $this->blogs = new ArrayCollection();

        $this->twoFactor = false;
    }

    #[PrePersist, PreUpdate]
    public function updateTimestamps(LifecycleEventArgs $args): void
    {
        if (! isset($this->createdAt)) {
            $this->joinedAt = new DateTime();
        }

        $this->updatedAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function hasTwoFactorAuthEnabled(): bool
    {
        return $this->twoFactor;
    }

    public function setTwoFactor(bool $twoFactor): static
    {
        $this->twoFactor = $twoFactor;

        return $this;
    }

    public function getVerifiedAt(): ?DateTime
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(DateTime $verifiedAt): static
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }

    public function getBlogs(): ArrayCollection|Collection
    {
        return $this->blogs;
    }

    public function addBlog(Blog $blog): static
    {
        $this->blogs->add($blog);

        return $this;
    }
}
