<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Date;

/**
 * @see https://tools.ietf.org/html/rfc7232#section-3.3
 */
final class IfModifiedSince extends Base
{
    public const NAME = 'If-Modified-Since';
}
