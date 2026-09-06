<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Auth;

use Emeq\ItheorieApi\Contracts\TokenStore;
use Emeq\ItheorieApi\Data\ItheorieCredentials;
use Illuminate\Contracts\Cache\Repository;

final readonly class CacheTokenStore implements TokenStore
{
    private const PREFIX = 'itheorie:token:';

    public function __construct(private Repository $cache) {}

    public function get(ItheorieCredentials $credentials): ?string
    {
        $token = $this->cache->get($this->key($credentials));

        return is_string($token) && $token !== '' ? $token : null;
    }

    public function put(ItheorieCredentials $credentials, string $token): void
    {
        $this->cache->forever($this->key($credentials), $token);
    }

    private function key(ItheorieCredentials $credentials): string
    {
        return self::PREFIX.$credentials->fingerprint();
    }
}
