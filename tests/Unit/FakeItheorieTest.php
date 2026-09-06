<?php

declare(strict_types=1);

use Emeq\ItheorieApi\Data\PurchaseRequest;
use Emeq\ItheorieApi\Enums\ErrorKind;
use Emeq\ItheorieApi\Exceptions\ItheorieException;
use Emeq\ItheorieApi\Testing\FakeItheorie;
use Saloon\Http\Faking\MockClient;

afterEach(function (): void {
    MockClient::destroyGlobal();
});

it('doet geen enkel verzoek en levert een bruikbare toegangscode', function (): void {
    $mock = MockClient::global([]);

    $purchase = new FakeItheorie()->createPurchase(new PurchaseRequest('fake-course-b', 'Jan', 'jan@example.com'));

    expect($purchase['access_code'])->toBeString()->not->toBeEmpty()
        ->and($purchase['direct_login_url'])->toContain($purchase['access_code']);

    $mock->assertNothingSent();
});

it('geeft twee aankopen verschillende toegangscodes', function (): void {
    $fake = new FakeItheorie;
    $request = new PurchaseRequest('fake-course-b', 'Jan', 'jan@example.com');

    expect($fake->createPurchase($request)['access_code'])
        ->not->toBe($fake->createPurchase($request)['access_code']);
});

it('onthoudt een aankoop zodat hij daarna opvraagbaar is', function (): void {
    $fake = new FakeItheorie;
    $created = $fake->createPurchase(new PurchaseRequest('fake-course-b', 'Jan', 'jan@example.com'));

    expect($fake->purchase($created['id'])['access_code'])->toBe($created['access_code'])
        ->and($fake->purchases()['data'])->toHaveCount(1);
});

it('werpt dezelfde getypeerde fout als de echte client bij een onbekende aankoop', function (): void {
    expect(fn () => new FakeItheorie()->purchase('bestaat-niet'))
        ->toThrow(ItheorieException::class);

    try {
        new FakeItheorie()->course('bestaat-niet');
    } catch (ItheorieException $e) {
        expect($e->kind)->toBe(ErrorKind::NotFound)->and($e->partnerCode)->toBe(404004);
    }
});

it('draagt geen productie-cursus-ids', function (): void {
    $ids = array_column(new FakeItheorie()->courses()['data'], 'id');

    expect($ids)->each->toStartWith('fake-course-');
});
