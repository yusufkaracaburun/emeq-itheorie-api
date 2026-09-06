<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Support;

final class PurchaseLocation
{
    public static function idFrom(string $location): ?string
    {
        $path = parse_url($location, PHP_URL_PATH);

        if (! is_string($path)) {
            return null;
        }

        $segments = array_values(array_filter(explode('/', $path), static fn (string $s): bool => $s !== ''));
        $index = array_search('purchases', $segments, true);

        if ($index === false || ! isset($segments[$index + 1])) {
            return null;
        }

        return $segments[$index + 1];
    }
}
