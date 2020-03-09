<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Cache;

/**
 * @see https://tools.ietf.org/html/rfc7234#section-5.4
 */
final class Pragma extends CacheControl
{
    public const NAME = 'Pragma';
}
