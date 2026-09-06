<?php

declare(strict_types=1);

use Emeq\ItheorieApi\Contracts\TokenStore;
use Emeq\ItheorieApi\Data\ItheorieCredentials;
use Emeq\ItheorieApi\Data\PurchaseRequest;
use Emeq\ItheorieApi\Enums\ErrorKind;
use Emeq\ItheorieApi\Exceptions\ItheorieException;
use Emeq\ItheorieApi\Itheorie;
use Emeq\ItheorieApi\Tests\Support\StaticCredentialResolver;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

afterEach(function (): void {
    MockClient::destroyGlobal();
});

function authOk(string $token = 'jwt-1'): MockResponse
{
    return MockResponse::make(['token' => $token]);
}

function partnerError(int $status, int $code, string $message, array $extra = []): MockResponse
{
    return MockResponse::make([
        'status' => $status,
        'code' => $code,
        'message' => $message,
        'description' => 'beschrijving',
    ] + $extra, $status);
}

it('haalt eerst een token op en gebruikt daarna de bearer', function (): void {
    $mock = MockClient::global([
        authOk(),
        MockResponse::make(['data' => [], 'links' => []]),
    ]);

    itheorie()->courses();

    $mock->assertSentCount(2);

    $sent = sentRequests($mock);

    expect($sent[0]->getUrl())->toEndWith('/auth')
        ->and($sent[1]->headers()->get('Authorization'))->toBe('Bearer jwt-1');
});

it('zet het resellernummer in elk pad', function (string $method, array $args, string $expected): void {
    MockClient::global([
        authOk(),
        MockResponse::make(['data' => [], 'links' => [], 'id' => 'p-1']),
    ]);

    itheorie()->{$method}(...$args);

    expect(sentUrls(MockClient::global())[1])->toContain('/12345678'.$expected);
})->with([
    ['courses', [], '/courses'],
    ['course', ['c-1'], '/courses/c-1'],
    ['purchases', [], '/purchases'],
    ['purchase', ['p-1'], '/purchases/p-1'],
    ['student', ['ABC'], '/students/ABC'],
    ['studentDetailed', ['ABC'], '/students/ABC/detailed'],
]);

it('stuurt paginatie mee als queryparameters', function (): void {
    $mock = MockClient::global([
        authOk(),
        MockResponse::make(['data' => [], 'links' => []]),
    ]);

    itheorie()->courses(page: 3, limit: 10);

    expect(sentRequests($mock)[1]->query()->all())->toBe(['page' => 3, 'limit' => 10]);
});

it('hergebruikt het token uit de store en authentiseert niet opnieuw', function (): void {
    $mock = MockClient::global([
        authOk(),
        MockResponse::make(['data' => [], 'links' => []]),
        MockResponse::make(['data' => [], 'links' => []]),
    ]);

    $itheorie = itheorie();
    $itheorie->courses();
    $itheorie->courses();

    $mock->assertSentCount(3);
});

it('vernieuwt een ingetrokken token en slaagt daarna alsnog', function (): void {
    $mock = MockClient::global([
        authOk('jwt-1'),
        partnerError(401, 401004, 'Token is revoked'),
        authOk('jwt-2'),
        MockResponse::make(['data' => [['id' => 'c-1']], 'links' => []]),
    ]);

    $courses = itheorie()->courses();

    $mock->assertSentCount(4);
    expect($courses['data'][0]['id'])->toBe('c-1');
});

it('probeert precies één keer opnieuw bij een blijvend ingetrokken token', function (): void {
    $mock = MockClient::global([
        authOk('jwt-1'),
        partnerError(401, 401004, 'Token is revoked'),
        authOk('jwt-2'),
        partnerError(401, 401004, 'Token is revoked'),
    ]);

    expect(fn () => itheorie()->courses())
        ->toThrow(ItheorieException::class);

    $mock->assertSentCount(4);
});

it('probeert niet opnieuw bij een broker-authenticatiefout', function (): void {
    $mock = MockClient::global([
        authOk(),
        partnerError(401, 401010, 'Broker not found'),
    ]);

    expect(fn () => itheorie()->courses())->toThrow(ItheorieException::class);

    $mock->assertSentCount(2);
});

it('draagt violations mee in een validatiefout', function (): void {
    MockClient::global([
        authOk(),
        partnerError(400, 400003, 'Incorrect data entered', [
            'violations' => [
                ['code' => 'not_blank', 'message' => 'Mag niet leeg zijn', 'propertyPath' => 'email'],
            ],
        ]),
    ]);

    try {
        itheorie()->createPurchase(new PurchaseRequest('c-1', 'Jan', 'jan@example.com'));
        $this->fail('verwachtte een ItheorieException');
    } catch (ItheorieException $e) {
        expect($e->kind)->toBe(ErrorKind::Validation)
            ->and($e->partnerCode)->toBe(400003)
            ->and($e->violations)->toBe([
                ['code' => 'not_blank', 'message' => 'Mag niet leeg zijn', 'propertyPath' => 'email'],
            ]);
    }
});

it('koopt een code en geeft de genormaliseerde aankoop terug', function (): void {
    $mock = MockClient::global([
        authOk(),
        MockResponse::make([
            'id' => 'p-1',
            'accessCode' => 'ABC1234',
            'directLoginUrl' => 'https://itheorie.nl/login/ABC1234',
            'expiresAt' => null,
        ]),
    ]);

    $purchase = itheorie()->createPurchase(
        new PurchaseRequest('c-1', 'Jan', 'jan@example.com', permissionToShareProgress: true),
    );

    expect($purchase['access_code'])->toBe('ABC1234')
        ->and($purchase['expires_at'])->toBeNull();

    expect(sentRequests($mock)[1]->body()?->all())->toBe([
        'course' => 'c-1',
        'name' => 'Jan',
        'email' => 'jan@example.com',
        'permissionToShareProgress' => true,
    ]);
});

it('volgt een aankoop-antwoord zonder body via de Location-header', function (): void {
    $mock = MockClient::global([
        authOk(),
        MockResponse::make([], 303, ['Location' => '/12345678/purchases/p-9']),
        MockResponse::make(['id' => 'p-9', 'accessCode' => 'XYZ9999']),
    ]);

    $purchase = itheorie()->createPurchase(new PurchaseRequest('c-1', 'Jan', 'jan@example.com'));

    expect($purchase['id'])->toBe('p-9')
        ->and($purchase['access_code'])->toBe('XYZ9999');

    $mock->assertSentCount(3);
});

it('weigert een aankoopbevestiging zonder id en zonder Location', function (): void {
    MockClient::global([
        authOk(),
        MockResponse::make([], 303),
    ]);

    expect(fn () => itheorie()->createPurchase(new PurchaseRequest('c-1', 'Jan', 'jan@example.com')))
        ->toThrow(ItheorieException::class);
});

it('faalt zichtbaar wanneer het auth-antwoord geen token draagt', function (): void {
    MockClient::global([
        MockResponse::make(['geen' => 'token']),
    ]);

    expect(fn () => itheorie()->courses())
        ->toThrow(ItheorieException::class, 'iTheorie gaf een authenticatie-antwoord zonder token.');
});

it('geeft een lege collectie wanneer iTheorie geen data-sleutel stuurt', function (): void {
    MockClient::global([
        authOk(),
        MockResponse::make(['links' => []]),
    ]);

    expect(itheorie()->courses()['data'])->toBe([]);
});

it('verstuurt een aankoop nooit opnieuw, ook niet bij een 5xx', function (): void {
    $mock = MockClient::global([
        authOk(),
        MockResponse::make(['status' => 502, 'code' => 0, 'message' => 'Bad gateway'], 502),
    ]);

    expect(fn () => itheorie()->createPurchase(new PurchaseRequest('c-1', 'Jan', 'jan@example.com')))
        ->toThrow(ItheorieException::class);

    $mock->assertSentCount(2);
});

it('verstuurt een aankoop nooit opnieuw wanneer de verbinding wegvalt', function (): void {
    $mock = MockClient::global([
        authOk(),
        MockResponse::make(['status' => 504, 'code' => 0, 'message' => 'Gateway timeout'], 504),
    ]);

    expect(fn () => itheorie()->createPurchase(new PurchaseRequest('c-1', 'Jan', 'jan@example.com')))
        ->toThrow(ItheorieException::class);

    $mock->assertSentCount(2);
});

it('herstelt ook op een tokenfout die geen intrekking is', function (): void {
    $mock = MockClient::global([
        authOk('jwt-1'),
        partnerError(401, 401009, 'Token is invalid'),
        authOk('jwt-2'),
        MockResponse::make(['data' => [['id' => 'c-1']], 'links' => []]),
    ]);

    expect(itheorie()->courses()['data'][0]['id'])->toBe('c-1');

    $mock->assertSentCount(4);
});

it('gebruikt het verse token van een parallelle winnaar in plaats van opnieuw te authentiseren', function (): void {
    $creds = itheorieCreds();

    $store = new class implements TokenStore
    {
        /** @var list<string> */
        public array $tokens = ['jwt-oud', 'jwt-nieuw'];

        public int $puts = 0;

        public function get(ItheorieCredentials $credentials): ?string
        {
            return array_shift($this->tokens) ?? 'jwt-nieuw';
        }

        public function put(ItheorieCredentials $credentials, string $token): void
        {
            $this->puts++;
        }
    };

    $itheorie = new Itheorie(new StaticCredentialResolver($creds), $store, Cache::store('array')->getStore());

    $mock = MockClient::global([
        partnerError(401, 401004, 'Token is revoked'),
        MockResponse::make(['data' => [], 'links' => []]),
    ]);

    $itheorie->courses();

    expect(sentRequests($mock)[1]->headers()->get('Authorization'))->toBe('Bearer jwt-nieuw')
        ->and($store->puts)->toBe(0);

    $mock->assertSentCount(2);
});
