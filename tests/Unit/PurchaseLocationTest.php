<?php

declare(strict_types=1);

use Emeq\ItheorieApi\Support\PurchaseLocation;

it('haalt het aankoop-id uit een relatieve locatie', function (): void {
    expect(PurchaseLocation::idFrom('/12345678/purchases/01HB2X'))->toBe('01HB2X');
});

it('haalt het aankoop-id uit een absolute locatie', function (): void {
    expect(PurchaseLocation::idFrom('https://itheorie.nl/api/connect/12345678/purchases/01HB2X'))->toBe('01HB2X');
});

it('negeert een querystring', function (): void {
    expect(PurchaseLocation::idFrom('/12345678/purchases/01HB2X?include=course'))->toBe('01HB2X');
});

it('geeft null zonder purchases-segment', function (): void {
    expect(PurchaseLocation::idFrom('/12345678/courses/01HB2X'))->toBeNull();
});

it('geeft null wanneer de locatie op purchases eindigt', function (): void {
    expect(PurchaseLocation::idFrom('/12345678/purchases'))->toBeNull();
});
