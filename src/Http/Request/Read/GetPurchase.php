<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Http\Request\Read;

use Emeq\ItheorieApi\Http\Request\BaseRequest;

final class GetPurchase extends BaseRequest
{
    public function __construct(string $reseller, private readonly string $purchase)
    {
        parent::__construct($reseller);
    }

    protected function resellerPath(): string
    {
        return '/purchases/'.rawurlencode($this->purchase);
    }
}
