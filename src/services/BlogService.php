<?php

declare(strict_types=1);

namespace Src\Services;

use Src\Entities\User;
use Src\Providers\BlogProvider;

class BlogService
{
    public function __construct(
        private readonly BlogProvider $blogProvider
    ) {}

    public function totalBlogs(User $user): int
    {
        return count($this->allBlogs($user));
    }

    public function totalTicks(User $user): int
    {
        $totalTicks = 0;

        $blogs = $this->allBlogs($user);

        foreach ($blogs as $blog) {
            $totalTicks += $blog->getTicks();
        }

        return $totalTicks;
    }

    public function totalViews(User $user): int
    {
        $totalViews = 0;

        $blogs = $this->allBlogs($user);

        foreach ($blogs as $blog) {
            $totalViews += $blog->getViews();
        }

        return $totalViews;
    }

    public function averageViews(User $user): float|int
    {
        return $this->totalViews($user) / $this->totalBlogs($user);
    }

    public function allBlogs(User $user): array
    {
        return $this->blogProvider->getByUser($user);
    }
}
