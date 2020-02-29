<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\Header;

final class DefaultValue extends BaseHeaderValue
{
    public static function createHeader(string $headerName = 'Undefined'): Header
    {
        return new Header($headerName);
    }
}
