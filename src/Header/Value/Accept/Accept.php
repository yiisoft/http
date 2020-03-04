<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Accept;

use Yiisoft\Http\Header\AcceptHeader;
use Yiisoft\Http\Header\Value\BaseHeaderValue;
use Yiisoft\Http\Header\WithQualityParam;

/**
 * @see https://tools.ietf.org/html/rfc7231#section-5.3.2
 */
class Accept extends BaseHeaderValue implements WithQualityParam
{
    public const NAME = 'Accept';
    public const VALUE_SEPARATOR = '/';

    final public static function createHeader(): AcceptHeader
    {
        return new AcceptHeader(static::class);
    }
}
