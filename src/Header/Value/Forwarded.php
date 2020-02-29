<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\ListedValues;
use Yiisoft\Http\Header\WithParams;

/**
 * @see https://tools.ietf.org/html/rfc7239
 */
final class Forwarded extends BaseHeaderValue implements WithParams, ListedValues
{
    public const NAME = 'Forwarded';
}
