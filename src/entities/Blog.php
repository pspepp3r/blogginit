<?php

declare(strict_types=1);

namespace Src\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Src\Entities\Traits\HasTimestamps;

#[Entity, Table('blogs')]
#[HasLifecycleCallbacks]
class Blog
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(unique: true, options: ['unsigned' => true])]
    private string $uuid;

    #[Column]
    private string $title;

    #[Column(length: 2000)]
    private string $content;

    // #[Column]
    // private string $media;

    #[Column]
    private string $category;

    #[Column('is_visible', options: ['default' => false])]
    private bool $isVisible = true;

    #[Column]
    private int $ticks = 0;

    #[Column]
    private int $views = 0;

    #[OneToMany(Comment::class, 'blog', ['remove'])]
    private Collection $comments;

    #[OneToMany(Interactions::class, 'blog', ['persist', 'remove'])]
    private Collection $interactions;

    #[ManyToOne(inversedBy: 'blogs')]
    private User $user;

    // #[ManyToOne(inversedBy: 'blogs')]
    // private Category $category;

    public function __construct() {
        $this->comments = new ArrayCollection();
        $this->interactions = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getUUId(): string
    {
        return $this->uuid;
    }

    public function setUUId(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
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

    // public function getMedia(): string
    // {
    //     return $this->media;
    // }

    // public function setMedia(string $media): static
    // {
    //     $this->media = $media;

    //     return $this;
    // }

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

    public function decrementTicks(): static
    {
        $this->ticks--;

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

    public function getInteractions(): ArrayCollection|Collection
    {
        return $this->interactions;
    }

    public function addInteraction(Interactions $interaction): static
    {
        $this->interactions->add($interaction);

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

    // public function getCategory(): Category
    // {
    //     return $this->category;
    // }

    // public function setCategory(Category $category): static
    // {
    //     $this->category = $category;
    //     $category->addBlog($this);

    //     return $this;
    // }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }
}
