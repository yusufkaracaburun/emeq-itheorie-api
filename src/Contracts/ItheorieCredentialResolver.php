<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Contracts;

use Emeq\ItheorieApi\Data\ItheorieCredentials;

interface ItheorieCredentialResolver
{
    public function resolve(): ItheorieCredentials;
}
