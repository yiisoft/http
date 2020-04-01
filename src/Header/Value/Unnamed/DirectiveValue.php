<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Unnamed;

use Yiisoft\Http\Header\DirectiveHeader;
use Yiisoft\Http\Header\Internal\DirectivesHeaderValue;

final class DirectiveValue extends DirectivesHeaderValue
{
    public static function createHeader(string $headerName = 'Undefined'): DirectiveHeader
    {
        return new DirectiveHeader($headerName);
    }
}
