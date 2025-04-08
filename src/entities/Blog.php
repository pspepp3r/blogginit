<?php

declare(strict_types=1);

namespace Src\Entities;

use DateTime;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Src\Entities\Traits\HasTimestamps;

#[Entity, Table('blogs')]
class Blog
{
    use HasTimestamps;
    
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $title;

    #[Column]
    private string $content;

    #[Column]
    private string $media;

    #[Column('is_visible', options: ['default' => false])]
    private bool $isVisible;

    #[Column]
    private int $ticks;

    #[Column]
    private int $views;

    #[OneToMany(Comment::class, 'blog', ['remove'])]
    private Collection $comments;

    #[ManyToOne(inversedBy: 'blogs')]
    private User $user;

    public function __construct() {
        $this->comments = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getMedia(): string
    {
        return $this->media;
    }

    public function setMedia(string $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function getIsVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): static
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getTicks(): int
    {
        return $this->ticks;
    }

    public function incrementTicks(): static
    {
        $this->ticks++;

        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function incrementViews(): static
    {
        $this->views++;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        $user->addBlog($this);

        return $this;
    }
}
