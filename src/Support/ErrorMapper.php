<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Support;

use Emeq\ItheorieApi\Enums\ErrorKind;
use Emeq\ItheorieApi\Exceptions\ItheorieException;
use Saloon\Http\Response;
use Throwable;

final class ErrorMapper
{
    public static function fromResponse(Response $response, ?Throwable $previous = null): ItheorieException
    {
        $status = $response->status();

        $body = [];

        try {
            $decoded = $response->json();
            $body = is_array($decoded) ? $decoded : [];
        } catch (Throwable) {
            $body = [];
        }

        $code = (int) ($body['code'] ?? 0);
        $kind = ErrorKind::fromPartnerCode($status, $code);

        $message = is_string($body['message'] ?? null) && $body['message'] !== ''
            ? $body['message']
            : 'iTheorie gaf HTTP '.$status;

        $violations = [];

        foreach ($body['violations'] ?? [] as $violation) {
            if (is_array($violation)) {
                $violations[] = [
                    'code' => (string) ($violation['code'] ?? ''),
                    'message' => (string) ($violation['message'] ?? ''),
                    'propertyPath' => (string) ($violation['propertyPath'] ?? ''),
                ];
            }
        }

        return new ItheorieException(
            message: $message,
            kind: $kind,
            status: $status,
            partnerCode: $code,
            violations: $violations,
            previous: $previous,
        );
    }
}
