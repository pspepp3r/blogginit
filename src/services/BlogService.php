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
        return $this->blogProvider->countTotalBlogs($user);
    }

    public function totalTicks(User $user): int
    {
        return $this->blogProvider->countTotalTicks($user);
    }

    public function totalViews(User $user): int
    {
        return $this->blogProvider->countTotalViews($user);
    }

    public function averageViews(User $user): float|int
    {
        return $this->blogProvider->countAverageViews($user);
    }

    public function allBlogs(User $user): array
    {
        return $this->blogProvider->getAllBlogs($user);
    }
}
