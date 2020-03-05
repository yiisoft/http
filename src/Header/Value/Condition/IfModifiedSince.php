<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Condition;

use Yiisoft\Http\Header\Value\Cache;

/**
 * @see https://tools.ietf.org/html/rfc7232#section-3.3
 */
final class IfModifiedSince extends Date
{
    public const NAME = 'If-Modified-Since';
}
