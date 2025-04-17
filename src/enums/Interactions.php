<?php

declare(strict_types=1);

namespace Src\Enums;

enum Interactions: string {
    case Tick = 'tick';
    case Comment = 'comment';
    // case View = 'view';
}
