<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Contracts;

use Emeq\ItheorieApi\Data\ItheorieCredentials;

/**
 * iTheorie kent geen token-expiry: een nieuw token trekt het vorige in (401004).
 * Twee processen die los authentiseren slaan elkaars token dus permanent om, dus
 * elke implementatie moet één gedeelde waarde teruggeven en refresh serialiseren.
 */
interface TokenStore
{
    public function get(ItheorieCredentials $credentials): ?string;

    public function put(ItheorieCredentials $credentials, string $token): void;
}
