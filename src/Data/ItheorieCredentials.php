<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Data;

final readonly class ItheorieCredentials
{
    public function __construct(
        public string $username,
        public string $password,
        public string $reseller,
        public string $baseUrl = 'https://itheorie.nl/api/connect',
    ) {}

    public function fingerprint(): string
    {
        return substr(hash('sha256', $this->baseUrl.'|'.$this->username), 0, 12);
    }
}
