<?php

declare(strict_types=1);

namespace Src\Services;

use Src\Entities\Blog;
use Src\Entities\User;
use Src\Providers\BlogProvider;
use Psr\Http\Message\ServerRequestInterface;
use Src\Data_objects\CreateBlogData;

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

    public function addView(string $uuid): ?Blog
    {
        $blog = $this->blogProvider->getByUUId($uuid);
        if ($blog) {
            $blog->incrementViews();
            $this->blogProvider->sync($blog);
        }

        return $blog;
    }

    public function create(CreateBlogData $data): bool
    {
        return $this->blogProvider->createBlog(
            $data,
            str_replace(' ', '-', strtolower($data->title))
        );
    }

    public function delete(string $uuid): void
    {
        $blog = $this->blogProvider->getByUUId($uuid);

        $this->blogProvider->deleteBlog($blog);
    }
}
