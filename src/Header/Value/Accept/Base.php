<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Accept;

use Yiisoft\Http\Header\AcceptHeader;
use Yiisoft\Http\Header\Value\BaseHeaderValue;
use Yiisoft\Http\Header\WithQualityParam;

abstract class Base extends BaseHeaderValue implements WithQualityParam
{
    public static function createHeader(): AcceptHeader
    {
        return new AcceptHeader(static::class);
    }
}
