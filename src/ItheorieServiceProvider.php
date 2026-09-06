<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi;

use Emeq\ItheorieApi\Auth\CacheTokenStore;
use Emeq\ItheorieApi\Contracts\ItheorieCredentialResolver;
use Emeq\ItheorieApi\Contracts\TokenStore;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

final class ItheorieServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bindIf(TokenStore::class, fn (): TokenStore => new CacheTokenStore($this->app->make(Repository::class)));

        $this->app->scoped(Itheorie::class, fn (): Itheorie => new Itheorie(
            $this->app->make(ItheorieCredentialResolver::class),
            $this->app->make(TokenStore::class),
            $this->lockProvider(),
        ));
    }

    private function lockProvider(): LockProvider
    {
        $store = $this->app->make(Repository::class)->getStore();

        if (! $store instanceof LockProvider) {
            throw new RuntimeException(
                'De cache-store ondersteunt geen atomic locks. iTheorie deelt één token over alle '
                .'processen; zonder lock trekken die elkaars token in. Gebruik redis, memcached, '
                .'dynamodb, database of array.'
            );
        }

        return $store;
    }
}
