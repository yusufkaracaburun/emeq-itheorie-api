<?php

declare(strict_types=1);

use Emeq\ItheorieApi\Enums\ErrorKind;
use Emeq\ItheorieApi\Exceptions\ItheorieException;

it('bestempelt token-fouten als token', function (int $code): void {
    expect(ErrorKind::fromPartnerCode(401, $code))->toBe(ErrorKind::Token);
})->with([401004, 401005, 401006, 401007, 401008, 401009]);

it('bestempelt broker- en basic-fouten als authenticatie', function (int $code): void {
    expect(ErrorKind::fromPartnerCode(401, $code))->toBe(ErrorKind::Authentication);
})->with([401001, 401002, 401003, 401010, 401011, 401012]);

it('bestempelt uitgeschakelde accounts als forbidden', function (int $code): void {
    expect(ErrorKind::fromPartnerCode(403, $code))->toBe(ErrorKind::Forbidden);
})->with([403001, 403002, 403003, 403004, 403005, 403006]);

it('bestempelt reseller-configuratiefouten als reseller', function (int $code, int $status): void {
    expect(ErrorKind::fromPartnerCode($status, $code))->toBe(ErrorKind::Reseller);
})->with([
    [400010, 400],
    [400020, 400],
    [400021, 400],
    [404001, 404],
    [404002, 404],
    [404003, 404],
    [404009, 404],
]);

it('bestempelt ontbrekende entiteiten als not found', function (int $code): void {
    expect(ErrorKind::fromPartnerCode(404, $code))->toBe(ErrorKind::NotFound);
})->with([404004, 404005, 404006, 404007, 404008]);

it('bestempelt onleesbare aanvragen als bad request', function (int $code): void {
    expect(ErrorKind::fromPartnerCode(400, $code))->toBe(ErrorKind::BadRequest);
})->with([400001, 400002, 400011, 400012]);

it('bestempelt 400003 als validatiefout', function (): void {
    expect(ErrorKind::fromPartnerCode(400, 400003))->toBe(ErrorKind::Validation);
});

it('bestempelt 503 zonder foutcode als service unavailable', function (): void {
    expect(ErrorKind::fromPartnerCode(503, 0))->toBe(ErrorKind::ServiceUnavailable);
});

it('valt terug op unknown bij een onbekende combinatie', function (int $status, int $code): void {
    expect(ErrorKind::fromPartnerCode($status, $code))->toBe(ErrorKind::Unknown);
})->with([
    [500, 500001],
    [500, 500002],
    [418, 418001],
    [400, 0],
]);

it('behandelt elke tokenfout als een verlopen token, niet alleen een intrekking', function (int $code): void {
    expect(new ItheorieException('token', ErrorKind::Token, 401, $code)->isStaleToken())->toBeTrue();
})->with([401004, 401005, 401006, 401007, 401008, 401009]);

it('behandelt een broker-authenticatiefout niet als een verlopen token', function (): void {
    expect(new ItheorieException('basic', ErrorKind::Authentication, 401, 401001)->isStaleToken())->toBeFalse();
});
