# emeq/itheorie-api

Laravel-SDK voor de [iTheorie Connect API](https://github.com/lensmedia/itheorie.nl-public)
van LENS Verkeersleermiddelen. Saloon v4, alleen HTTP, auth en DTO's. Geen
business-logica, geen opslag, geen migraties.

## Waarom er precies één beller mag zijn

iTheorie kent geen token-expiry. Een nieuw token trekt het vorige in:

> `401004 token_is_revoked` — "occurs when new token generated"
> (`docs/partners/itheorie/error-codes.md`)

Twee processen die los authentiseren met dezelfde broker-inlog slaan elkaars token
dus permanent om. Daarom bezit de SDK geen eigen tokenopslag maar vraagt hij er een
via `TokenStore`, en hoort er per credential precies één host te zijn die belt.

## Installatie

```bash
composer require emeq/itheorie-api
```

## Aansluiten

De host levert de credentials. `TokenStore` is optioneel: zonder eigen binding
gebruikt de SDK `CacheTokenStore` op de standaard cache.

```php
use Emeq\ItheorieApi\Contracts\ItheorieCredentialResolver;
use Emeq\ItheorieApi\Data\ItheorieCredentials;

final readonly class HubItheorieCredentialResolver implements ItheorieCredentialResolver
{
    public function resolve(): ItheorieCredentials
    {
        return new ItheorieCredentials(
            username: $settings->username,
            password: $settings->password,
            reseller: $settings->reseller,
        );
    }
}
```

```php
$this->app->bind(ItheorieCredentialResolver::class, HubItheorieCredentialResolver::class);
```

## Gebruik

```php
use Emeq\ItheorieApi\Data\PurchaseRequest;
use Emeq\ItheorieApi\Itheorie;

$itheorie = app(Itheorie::class);

$itheorie->courses();
$itheorie->course('01GC7ABB22TT7Y6883YPVHFCG5');

$purchase = $itheorie->createPurchase(new PurchaseRequest(
    course: '01GC7ABB22TT7Y6883YPVHFCG5',
    name: 'Jan Jansen',
    email: 'jan@example.com',
));

$purchase['access_code'];
$purchase['direct_login_url'];
$purchase['expires_at'];

$itheorie->purchase($purchase['id']);
$itheorie->purchases(page: 1, limit: 50);
$itheorie->student($purchase['access_code']);
$itheorie->studentDetailed($purchase['access_code']);
```

Antwoorden zijn genormaliseerd naar snake_case. `expires_at` kan `null` zijn.

## Fouten

Elke partnerfout komt terug als `ItheorieException` met een `ErrorKind`, de
partner-`code` en bij validatie de `violations`. Saloon-exceptions lekken niet naar
de aanroeper.

```php
try {
    $itheorie->createPurchase($request);
} catch (ItheorieException $e) {
    $e->kind;        // ErrorKind::Validation
    $e->partnerCode; // 400003
    $e->violations;  // [['code' => ..., 'message' => ..., 'propertyPath' => ...]]
}
```

Een ingetrokken token (`401004`) wordt precies één keer hersteld: de SDK vergeet het
token, authentiseert opnieuw en herhaalt de aanroep. Andere tokenfouten worden niet
herhaald.

## Testen zonder te kopen

Er is geen bruikbare testomgeving: `test.itheorie.nl` antwoordt 403 (gemeten
2026-09-05). Elke echte aankoop wordt gefactureerd. Gebruik daarom
`FakeItheorie`, die geen enkel verzoek doet en aangemaakte aankopen onthoudt.

```php
$this->app->bind(Itheorie::class, FakeItheorie::class);
```

Bind hem nooit in productie.

## Partner-documentatie

`docs/partners/itheorie/` bevat de officiële documentatie zoals gepubliceerd door
LENS. Endpoints, veldnamen en foutcodes volgen die bestanden, niet aannames.

Let op: op `test.itheorie.nl` kunnen foutantwoorden extra, mogelijk gevoelige
velden bevatten. Log geen ruwe foutbodies.

## Ontwikkelen

```bash
composer test
composer analyse
composer format
```
