<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Http\Request\Read;

use Emeq\ItheorieApi\Http\Request\BaseRequest;

final class GetCourse extends BaseRequest
{
    public function __construct(string $reseller, private readonly string $course)
    {
        parent::__construct($reseller);
    }

    protected function resellerPath(): string
    {
        return '/courses/'.rawurlencode($this->course);
    }
}
