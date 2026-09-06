<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Exceptions;

use Emeq\ItheorieApi\Enums\ErrorKind;
use RuntimeException;
use Throwable;

class ItheorieException extends RuntimeException
{
    /**
     * @param  list<array{code: string, message: string, propertyPath: string}>  $violations
     */
    public function __construct(
        string $message,
        public readonly ErrorKind $kind,
        public readonly int $status,
        public readonly int $partnerCode,
        public readonly array $violations = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $status, $previous);
    }

    public function isStaleToken(): bool
    {
        return $this->kind === ErrorKind::Token;
    }
}
