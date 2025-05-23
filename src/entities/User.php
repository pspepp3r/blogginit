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
use Src\Enums\ColorScheme;

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

    #[Column(nullable: true)]
    private string $gender = 'male';

    #[Column(nullable: true)]
    private string $occupation = '';

    #[Column(nullable: true)]
    private string $location = '';

    #[Column(nullable: true)]
    private string $introduction = '';

    #[Column('color_scheme', enumType: ColorScheme::class, options: ['default' => 'light'])]
    private ColorScheme $colorScheme = ColorScheme::Light;

    #[Column('two_factor',  options: ['default' => false])]
    private bool $twoFactor = false;

    #[Column('verified_at', nullable: true)]
    private ?DateTime $verifiedAt;

    #[Column('joined_at')]
    private DateTime $joinedAt;

    #[Column('updated_at')]
    private DateTime $updatedAt;

    #[OneToMany(Blog::class, 'user')]
    private Collection $blogs;

    #[OneToMany(Sessions::class, 'user', ['persist', 'remove'])]
    private Collection $sessions;

    #[OneToMany(Interactions::class, 'user', ['persist', 'remove'])]
    private Collection $interactions;

    #[OneToMany(Comment::class, 'user', ['persist', 'remove'])]
    private Collection $comments;

    public function __construct()
    {
        $this->blogs = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->interactions = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getOccupation(): string
    {
        return $this->occupation;
    }

    public function setOccupation(string $occupation): static
    {
        $this->occupation = $occupation;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getIntroduction(): string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): static
    {
        $this->introduction = $introduction;

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

    public function getColorScheme(): ColorScheme
    {
        return $this->colorScheme;
    }

    public function setColorScheme($colorScheme): static
    {
        $this->colorScheme = $colorScheme;

        return $this;
    }

    public function getInteractions(): ArrayCollection|Collection
    {
        return $this->interactions;
    }

    public function addInteraction(Interactions $interaction): static
    {
        $this->interactions->add($interaction);

        return $this;
    }
    
    public function getComments(): ArrayCollection|Collection
    {
        return $this->comments;
    }
    public function addComment(Comment $comment): static
    {
        $this->comments->add($comment);

        return $this;
    }
}
