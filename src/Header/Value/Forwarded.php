<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\Internal\WithParamsHeaderValue;

/**
 * @link https://tools.ietf.org/html/rfc7239
 */
final class Forwarded extends WithParamsHeaderValue
{
    public const NAME = 'Forwarded';

    protected const PARSING_LIST = true;
}
