<?php

declare(strict_types=1);

use Emeq\ItheorieApi\Support\Normalize;

it('normaliseert een aankoop naar snake_case', function (): void {
    $purchase = Normalize::purchase([
        'id' => 'p-1',
        'invoice' => 42,
        'createdAt' => '2026-09-06T10:00:00+02:00',
        'price' => ['amount' => '9.70', 'currency' => 'EUR'],
        'subscription' => 's-1',
        'accessCode' => 'ABC123',
        'expiresAt' => null,
        'loginUrl' => 'https://itheorie.nl/login',
        'directLoginUrl' => 'https://itheorie.nl/login/ABC123',
        'name' => 'Jan',
        'email' => 'jan@example.com',
        'mobilePhoneNumber' => '0612345678',
    ]);

    expect($purchase['access_code'])->toBe('ABC123')
        ->and($purchase['direct_login_url'])->toBe('https://itheorie.nl/login/ABC123')
        ->and($purchase['mobile_phone_number'])->toBe('0612345678')
        ->and($purchase['expires_at'])->toBeNull();
});

it('accepteert een aankoop die al snake_case draagt', function (): void {
    $purchase = Normalize::purchase(['access_code' => 'XYZ', 'direct_login_url' => 'https://x']);

    expect($purchase['access_code'])->toBe('XYZ')
        ->and($purchase['direct_login_url'])->toBe('https://x');
});

it('geeft null voor ontbrekende aankoopvelden', function (): void {
    expect(Normalize::purchase([]))->toBe([
        'id' => null,
        'invoice' => null,
        'created_at' => null,
        'price' => null,
        'subscription' => null,
        'access_code' => null,
        'expires_at' => null,
        'login_url' => null,
        'direct_login_url' => null,
        'name' => null,
        'email' => null,
        'mobile_phone_number' => null,
    ]);
});

it('vlakt de aanbieding af tot bedragen', function (): void {
    $offer = Normalize::offer([
        'originalPrice' => ['amount' => '12.50', 'currency' => 'EUR'],
        'currentPrice' => ['amount' => '9.70', 'currency' => 'EUR'],
        'currentPriceDetails' => 'staffel',
        'vat' => 0.21,
        'suggestedRetailPrice' => ['amount' => '39.95', 'currency' => 'EUR'],
    ]);

    expect($offer)->toBe([
        'original_price' => 12.5,
        'current_price' => 9.7,
        'current_price_details' => 'staffel',
        'vat' => 0.21,
        'suggested_retail_price' => 39.95,
    ]);
});

it('geeft nul terug voor een ontbrekend bedrag in plaats van te struikelen', function (): void {
    $offer = Normalize::offer([]);

    expect($offer['original_price'])->toBe(0.0)
        ->and($offer['current_price'])->toBe(0.0)
        ->and($offer['suggested_retail_price'])->toBe(0.0)
        ->and($offer['vat'])->toBe(0);
});

it('valt terug op euro wanneer de valuta ontbreekt', function (): void {
    expect(Normalize::money(['amount' => '5']))->toBe(['amount' => 5.0, 'currency' => 'EUR'])
        ->and(Normalize::money([]))->toBe(['amount' => 0.0, 'currency' => 'EUR']);
});

it('laat de Planny-specifieke product_id uit een cursus', function (): void {
    expect(Normalize::course(['id' => 'c-1']))->not->toHaveKey('product_id');
});

it('nest de aanbieding in een cursus en laat hem weg wanneer die ontbreekt', function (): void {
    $withOffer = Normalize::course(['id' => 'c-1', 'offer' => ['currentPrice' => ['amount' => '9.70']]]);
    $without = Normalize::course(['id' => 'c-1']);

    expect($withOffer['offer']['current_price'])->toBe(9.7)
        ->and($without['offer'])->toBeNull();
});

it('normaliseert de voortgang van een leerling', function (): void {
    $subscription = Normalize::subscription([
        'id' => 's-1',
        'accessCode' => 'ABC',
        'progressUrl' => 'https://x',
        'progression' => [
            'chapters' => ['total' => 10, 'completed' => 3, 'progress' => 30, 'chapters' => []],
            'theoryLessons' => ['total' => 4, 'attended' => 1, 'progress' => 25, 'reservations' => []],
        ],
    ]);

    expect($subscription['access_code'])->toBe('ABC')
        ->and($subscription['progress_url'])->toBe('https://x')
        ->and($subscription['progression']['chapters']['completed'])->toBe(3)
        ->and($subscription['progression']['theory_lessons']['attended'])->toBe(1)
        ->and($subscription['progression']['exams'])->toBeNull();
});

it('draagt de paginatielinks van een collectie over', function (): void {
    $collection = Normalize::collection([
        'links' => ['self' => '/p?page=2', 'next' => '/p?page=3'],
        'data' => [['id' => 'p-1'], ['id' => 'p-2']],
    ], Normalize::purchase(...));

    expect($collection['links']['self'])->toBe('/p?page=2')
        ->and($collection['links']['next'])->toBe('/p?page=3')
        ->and($collection['links']['first'])->toBeNull()
        ->and($collection['data'])->toHaveCount(2)
        ->and($collection['data'][0]['id'])->toBe('p-1');
});

it('geeft een lege collectie wanneer data ontbreekt', function (): void {
    expect(Normalize::collection([], Normalize::course(...))['data'])->toBe([]);
});
