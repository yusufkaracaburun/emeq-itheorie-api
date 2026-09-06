<?php

declare(strict_types=1);

use Emeq\ItheorieApi\Auth\CacheTokenStore;
use Emeq\ItheorieApi\Data\ItheorieCredentials;
use Emeq\ItheorieApi\Itheorie;
use Emeq\ItheorieApi\Tests\Support\StaticCredentialResolver;
use Emeq\ItheorieApi\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;

uses(TestCase::class)->in(__DIR__);

uses()->beforeEach(function (): void {
    Cache::flush();
})->in(__DIR__);

function itheorieCreds(): ItheorieCredentials
{
    return new ItheorieCredentials(
        username: 'lens-id',
        password: 'geheim',
        reseller: '12345678',
        baseUrl: 'https://itheorie.test/api/connect',
    );
}

function itheorie(?ItheorieCredentials $credentials = null): Itheorie
{
    $credentials ??= itheorieCreds();

    return new Itheorie(
        new StaticCredentialResolver($credentials),
        new CacheTokenStore(Cache::store('array')),
    );
}

/**
 * @return list<PendingRequest>
 */
function sentRequests(MockClient $mock): array
{
    return array_map(
        static fn (Response $response): PendingRequest => $response->getPendingRequest(),
        $mock->getRecordedResponses(),
    );
}

/**
 * @return list<string>
 */
function sentUrls(MockClient $mock): array
{
    return array_map(static fn (PendingRequest $r): string => $r->getUrl(), sentRequests($mock));
}
