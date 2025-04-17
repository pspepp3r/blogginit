<?php

declare(strict_types=1);

namespace Src\Providers;

use Src\Entities\Blog;
use Src\Entities\User;
use Doctrine\ORM\EntityManager;
use Src\Data_objects\CreateBlogData;

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

    public function createBlog(CreateBlogData $data, string $uuid): bool
    {
        $blog = new Blog();
        $blog->setUser($data->user);
        $blog->setTitle($data->title);
        $blog->setContent($data->content);
        $blog->setCategory($data->category);
        $blog->setUUId($uuid);

        $this->sync($blog);

        return true;
    }

    public function addTick(Blog $blog): void
    {
        $blog->incrementTicks();

        $this->sync($blog);
    }

    public function removeTick(Blog $blog): void
    {
        $blog->decrementTicks();

        $this->sync($blog);
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
