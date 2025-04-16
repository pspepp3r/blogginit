<?php

declare(strict_types=1);

namespace Src\Providers;

use Src\Entities\User;
use Doctrine\ORM\EntityManager;
use Src\Entities\Blog;

class BlogProvider
{
    public function __construct(
        private readonly EntityManager $entityManager,
    ) {}

    public function getByUser(User $user): array
    {
        return $this->entityManager->getRepository(Blog::class)
            ->findBy(['user' => (string) $user->getId()]);
    }

    public function getByUUId(string $uuid): ?Blog
    {
        return $this->entityManager->getRepository(Blog::class)
            ->findOneBy(['uuid' => $uuid]);
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Blog::class)
            ->findAll();
    }

    public function deleteBlog(Blog $blog): void
    {
        $this->entityManager->remove($blog);

        $this->entityManager->flush();
    }

    public function sync(Blog $blog): void
    {
        $this->entityManager->persist($blog);
        $this->entityManager->flush();
    }
}
