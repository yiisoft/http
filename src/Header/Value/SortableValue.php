<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\SortableHeader;
use Yiisoft\Http\Header\WithQualityParam;

final class SortableValue extends BaseHeaderValue implements WithQualityParam
{
    public static function createHeader(string $headerName = 'Undefined'): SortableHeader
    {
        return new SortableHeader($headerName);
    }
}
