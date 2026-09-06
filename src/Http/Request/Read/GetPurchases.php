<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Http\Request\Read;

use Emeq\ItheorieApi\Http\Request\BaseRequest;

final class GetPurchases extends BaseRequest
{
    public function __construct(
        string $reseller,
        private readonly int $page = 1,
        private readonly int $limit = 50,
    ) {
        parent::__construct($reseller);
    }

    protected function resellerPath(): string
    {
        return '/purchases';
    }

    protected function defaultQuery(): array
    {
        return ['page' => $this->page, 'limit' => $this->limit];
    }
}
