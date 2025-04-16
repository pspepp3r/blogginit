<?php

declare(strict_types=1);

namespace Src\Data_objects;

use Src\Entities\User;

class CreateBlogData
{
    public function __construct(
        public readonly User $user,
        public readonly string $title,
        public readonly string $content,
        public readonly string $category,
    ) {}
}
