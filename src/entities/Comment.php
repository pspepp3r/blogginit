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
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Src\Entities\Traits\HasTimestamps;

#[Entity, Table('comments')]
#[HasLifecycleCallbacks]
class Comment
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $text;

    #[Column]
    private int $ticks = 0;

    #[Column]
    private DateTime $createdAt;

    #[Column]
    private DateTime $updatedAt;

    #[OneToMany(Reply::class, 'comment', ['remove'])]
    private Collection $replies;

    #[ManyToOne(inversedBy: 'comments')]
    private Blog $blog;

    #[ManyToOne(inversedBy: 'comments')]
    private User $user;

    public function __construct()
    {
        $this->replies = new ArrayCollection();
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getTicks(): int
    {
        return $this->ticks;
    }

    public function setTicks(int $ticks): static
    {
        $this->ticks = $ticks;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(Reply $reply): static
    {
        $this->replies->add($reply);

        return $this;
    }

    public function getBlog(): Blog
    {
        return $this->blog;
    }

    public function setBlog(Blog $blog): static
    {
        $this->blog = $blog;
        $blog->addComment($this);

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        $user->addComment($this);

        return $this;
    }
}
