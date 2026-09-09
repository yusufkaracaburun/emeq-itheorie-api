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

    /**
     * iTheorie kent geen token-expiry, dus een gecacht token leeft door tot het
     * account eronder verdwijnt — bij een sandbox-rebuild gebeurt dat. Zo'n token
     * geeft 401010 tot 401012 in plaats van een token-code; opnieuw inloggen lost
     * het op, en bestaat de broker echt niet dan faalt de retry alsnog.
     */
    public function isStaleToken(): bool
    {
        return $this->kind === ErrorKind::Token
            || ($this->partnerCode >= 401010 && $this->partnerCode <= 401012);
    }
}
