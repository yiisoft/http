<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\Internal\BaseHeaderValue;

final class SimpleValue extends BaseHeaderValue
{
    public static function createHeader(string $headerName = 'Undefined'): Header
    {
        return new Header($headerName);
    }
}
