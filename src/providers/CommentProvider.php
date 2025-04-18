<?php

declare(strict_types=1);

namespace Src\Providers;

use Src\Entities\Blog;
use Src\Entities\User;
use Doctrine\ORM\EntityManager;
use Src\Entities\Comment;

class CommentProvider
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {}

    public function getByUser(User $user): array
    {
        return $this->entityManager->getRepository(Comment::class)
            ->findBy(['user' => (string) $user->getId()]);
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Comment::class)
            ->findAll();
    }

    public function createComment(User $user, Blog $blog, string $text): void
    {
        $comment = new Comment();
        $comment->setUser($user);
        $comment->setBlog($blog);
        $comment->setText($text);

        $this->sync($comment);
    }

    public function deleteComment(Comment $comment): void
    {
        $this->entityManager->remove($comment);

        $this->entityManager->flush();
    }

    public function sync(Comment $comment): void
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }
}
