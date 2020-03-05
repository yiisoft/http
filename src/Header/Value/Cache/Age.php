<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Cache;

use Yiisoft\Http\Header\Value\BaseHeaderValue;

/**
 * @see https://tools.ietf.org/html/rfc7234#section-5.1
 */
final class Age extends BaseHeaderValue
{
    public const NAME = 'Age';
}
