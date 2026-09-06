<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Tests\Support;

use Emeq\ItheorieApi\Contracts\ItheorieCredentialResolver;
use Emeq\ItheorieApi\Data\ItheorieCredentials;

final readonly class StaticCredentialResolver implements ItheorieCredentialResolver
{
    public function __construct(private ItheorieCredentials $credentials) {}

    public function resolve(): ItheorieCredentials
    {
        return $this->credentials;
    }
}
