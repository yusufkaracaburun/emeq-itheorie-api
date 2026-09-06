<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Http\Request\Write;

use Emeq\ItheorieApi\Data\PurchaseRequest;
use Emeq\ItheorieApi\Http\Request\BaseRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreatePurchase extends BaseRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * iTheorie draagt geen idempotency-key en dedupliceert niet, dus een tweede
     * verzending koopt een tweede code. Nooit opnieuw proberen.
     */
    public ?int $tries = 1;

    public function __construct(string $reseller, private readonly PurchaseRequest $purchase)
    {
        parent::__construct($reseller);
    }

    protected function resellerPath(): string
    {
        return '/purchases';
    }

    protected function defaultHeaders(): array
    {
        return ['Content-Type' => 'application/json'];
    }

    /**
     * @return array<string, scalar>
     */
    protected function defaultBody(): array
    {
        return $this->purchase->toArray();
    }
}
