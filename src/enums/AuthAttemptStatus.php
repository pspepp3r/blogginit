<?php

declare(strict_types = 1);

namespace Src\Enums;

enum AuthAttemptStatus
{
    case FAILED;
    case TWO_FACTOR_AUTH;
    case SUCCESS;
}
