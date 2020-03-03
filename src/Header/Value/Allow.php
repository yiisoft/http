<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\ListedValues;

/**
 * @see https://tools.ietf.org/html/rfc7231#section-7.4.1
 */
final class Allow extends BaseHeaderValue implements ListedValues
{
    public const NAME = 'Allow';
}
