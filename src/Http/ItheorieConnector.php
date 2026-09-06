<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Http;

use Emeq\ItheorieApi\Data\ItheorieCredentials;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Connector;
use Saloon\Http\Request;

class ItheorieConnector extends Connector
{
    public function __construct(
        private readonly ItheorieCredentials $credentials,
        private readonly int $timeoutSeconds = 30,
        private readonly int $connectTimeoutSeconds = 10,
        public ?int $tries = 2,
        public ?int $retryInterval = 250,
        public ?bool $throwOnMaxTries = false,
    ) {}

    public function resolveBaseUrl(): string
    {
        return rtrim($this->credentials->baseUrl, '/');
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function defaultConfig(): array
    {
        return [
            'timeout' => $this->timeoutSeconds,
            'connect_timeout' => $this->connectTimeoutSeconds,
        ];
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if ($exception instanceof FatalRequestException) {
            return true;
        }

        return in_array($exception->getResponse()->status(), [500, 502, 503, 504], true);
    }
}
