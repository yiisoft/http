<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\SortableHeader;
use Yiisoft\Http\Header\Internal\WithParamsHeaderValue;

final class SortedValue extends WithParamsHeaderValue
{
    protected const PARSING_LIST = true;
    protected const PARSING_Q_PARAM = true;

    public static function createHeader(string $headerName = 'Undefined'): SortableHeader
    {
        return new SortableHeader($headerName);
    }
}
