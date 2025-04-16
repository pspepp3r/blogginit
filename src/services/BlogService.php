<?php

declare(strict_types=1);

namespace Src\Services;

use Src\Entities\Blog;
use Src\Entities\User;
use Src\Providers\BlogProvider;
use Psr\Http\Message\ServerRequestInterface;

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
        if ($this->totalBlogs($user))
            return $this->totalViews($user) / $this->totalBlogs($user);

        return 0;
    }

    public function allBlogs(User $user): array
    {
        return $this->blogProvider->getByUser($user);
    }

    public function getRequestUUId(ServerRequestInterface $request): ?Blog
    {
        $requestUri = $request->getServerParams()['REQUEST_URI'];
        $uuid = \explode('/', $requestUri);

        if (isset($uuid[3]))
            return $this->blogProvider->getByUUId($uuid[3]);

        return $this->blogProvider->getByUUId($uuid[2]);
    }

    public function addView(ServerRequestInterface $request): ?Blog
    {
        $blog = $this->getRequestUUId($request);
        if ($blog) {
            $blog->incrementViews();
            $this->blogProvider->sync($blog);
        }

        return $blog;
    }

    public function delete(Blog $blog): void
    {
        $this->blogProvider->deleteBlog($blog);
    }
}
