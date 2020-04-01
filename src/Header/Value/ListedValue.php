<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\Internal\BaseHeaderValue;

final class ListedValue extends BaseHeaderValue
{
    protected const PARSING_LIST = true;

    public static function createHeader(string $headerName = 'Undefined'): Header
    {
        return new Header($headerName);
    }
}
