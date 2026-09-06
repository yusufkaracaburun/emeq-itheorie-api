<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Http\Request;

use Emeq\ItheorieApi\Data\ItheorieCredentials;
use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Request;

final class GetAuthToken extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly ItheorieCredentials $credentials) {}

    public function resolveEndpoint(): string
    {
        return '/auth';
    }

    protected function defaultAuth(): Authenticator
    {
        return new BasicAuthenticator($this->credentials->username, $this->credentials->password);
    }
}
