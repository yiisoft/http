<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\Internal\BaseHeaderValue;

/**
 * @link https://tools.ietf.org/html/rfc7231#section-7.4.1
 */
final class Allow extends BaseHeaderValue
{
    public const NAME = 'Allow';

    protected const PARSING_LIST = true;
}
