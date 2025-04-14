<?php

declare(strict_types=1);

namespace Src\Data_objects;

class UpdateUserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $gender,
        public readonly string $occupation,
        public readonly string $country,
        public readonly string $introduction,
        public readonly ?string $profile_picture
    ) {
    }
}
