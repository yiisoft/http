<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\WithQualityParam;

/**
 * @see https://tools.ietf.org/html/rfc7231#section-5.3.2
 */
final class Accept extends BaseHeaderValue implements WithQualityParam
{
    public const NAME = 'Accept';
}
