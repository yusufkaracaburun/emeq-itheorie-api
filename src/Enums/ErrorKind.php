<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Enums;

enum ErrorKind: string
{
    case Token = 'token';
    case Authentication = 'authentication';
    case Forbidden = 'forbidden';
    case Validation = 'validation';
    case Reseller = 'reseller';
    case NotFound = 'not_found';
    case BadRequest = 'bad_request';
    case ServiceUnavailable = 'service_unavailable';
    case Unknown = 'unknown';

    public static function fromPartnerCode(int $status, int $code): self
    {
        return match (true) {
            $code >= 401004 && $code <= 401009 => self::Token,
            $code >= 401001 && $code <= 401003 => self::Authentication,
            $code >= 401010 && $code <= 401012 => self::Authentication,
            $code >= 403001 && $code <= 403006 => self::Forbidden,
            $code === 400003 => self::Validation,
            in_array($code, [400010, 400020, 400021, 404001, 404002, 404003, 404009], true) => self::Reseller,
            $code >= 404004 && $code <= 404008 => self::NotFound,
            in_array($code, [400001, 400002, 400011, 400012], true) => self::BadRequest,
            $status === 503 => self::ServiceUnavailable,
            default => self::Unknown,
        };
    }
}
