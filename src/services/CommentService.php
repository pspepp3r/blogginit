<?php

declare(strict_types=1);

namespace Src\Services;

use Src\Entities\User;
use Src\Providers\BlogProvider;
use Src\Providers\CommentProvider;

class CommentService
{
    public function __construct(
        private readonly BlogProvider $blogProvider,
        private readonly CommentProvider $commentProvider,
    ) {}

    public function addComment(User $user, string $uuid, string $comment): void
    {
        $blog = $this->blogProvider->getByUUId($uuid);

        $this->commentProvider->createComment($user, $blog, $comment);
    }
}
