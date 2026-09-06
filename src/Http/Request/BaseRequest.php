<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Http\Request;

use Saloon\Enums\Method;
use Saloon\Http\Request;

abstract class BaseRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected readonly string $reseller) {}

    abstract protected function resellerPath(): string;

    public function resolveEndpoint(): string
    {
        return '/'.rawurlencode($this->reseller).$this->resellerPath();
    }
}
