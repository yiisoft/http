<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\ListedValues;

final class ListedValue extends BaseHeaderValue implements ListedValues
{
    public static function createHeader(string $headerName = 'Undefined'): Header
    {
        return new Header($headerName);
    }
}
