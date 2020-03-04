<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Date;

/**
 * @see https://tools.ietf.org/html/rfc7232#section-3.4
 */
final class IfUnmodifiedSince extends Date
{
    public const NAME = 'If-Unmodified-Since';
}
