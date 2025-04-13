<?php

declare(strict_types=1);

namespace Src\Providers;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Src\Entities\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Math;
use Src\Entities\Blog;
use Src\Services\MathService;

class BlogProvider
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly MathService $mathService
    ) {}

    public function countTotalBlogs(User $user): int
    {
        return $this->entityManager->getRepository(Blog::class)
        ->count(['user' => (string) $user->getId()]);
    }

    public function countTotalTicks(User $user): int
    {
        $totalTicks = 0;

        $blogs = $this->entityManager->getRepository(Blog::class)->findAll();

        foreach ($blogs as $blog) {
            $totalTicks += $blog->getTicks();
        }

        return $totalTicks;
    }

    public function countTotalViews(User $user): int
    {
        $totalTicks = 0;

        $blogs = $this->entityManager->getRepository(Blog::class)->findAll();

        foreach ($blogs as $blog) {
            $totalTicks += $blog->getViews();
        }

        return $totalTicks;
    }

    public function countAverageViews(User $user): float|int
    {
        $blogs = $this->entityManager->getRepository(Blog::class)->findAll();

        return $this->mathService->calculateAverage($blogs);
    }

    public function getAllBlogs(User $user): array
    {
        return $this->entityManager->getRepository(Blog::class)->findAll();
    }
}
