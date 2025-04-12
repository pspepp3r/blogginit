<?php

declare(strict_types=1);

namespace Src\Errors;

use Throwable;
use RuntimeException;

class LinkSignatureException extends RuntimeException
{
    public function __construct(
        public readonly string $error,
        string $message = 'Invalid / Expired Link',
        int $code = 401,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
