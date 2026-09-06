<?php

declare(strict_types=1);

use Emeq\ItheorieApi\Data\PurchaseRequest;

it('stuurt precies de sleutels die iTheorie verwacht', function (): void {
    $payload = new PurchaseRequest(
        course: '01GC7ABB22TT7Y6883YPVHFCG5',
        name: 'Jan Jansen',
        email: 'jan@example.com',
        mobilePhone: '0612345678',
        permissionToShareProgress: true,
    )->toArray();

    expect($payload)->toBe([
        'course' => '01GC7ABB22TT7Y6883YPVHFCG5',
        'name' => 'Jan Jansen',
        'email' => 'jan@example.com',
        'mobilePhone' => '0612345678',
        'permissionToShareProgress' => true,
    ]);
});

it('laat lege velden weg in plaats van null te sturen', function (): void {
    $payload = new PurchaseRequest(
        course: 'course-1',
        name: 'Jan',
        email: 'jan@example.com',
    )->toArray();

    expect($payload)->toBe([
        'course' => 'course-1',
        'name' => 'Jan',
        'email' => 'jan@example.com',
    ]);
});

it('verstuurt permissionToShareProgress false in plaats van hem weg te filteren', function (): void {
    $payload = new PurchaseRequest(
        course: 'course-1',
        name: 'Jan',
        email: 'jan@example.com',
        permissionToShareProgress: false,
    )->toArray();

    expect($payload)->toHaveKey('permissionToShareProgress')
        ->and($payload['permissionToShareProgress'])->toBeFalse();
});
