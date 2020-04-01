<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Parser;

class HeaderParsingParams
{
    public bool $valuesList = false;
    public bool $withParams = false;
    public bool $q = false;
    public bool $directives = false;
}
