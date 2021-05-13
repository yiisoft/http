<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\Internal\BaseHeaderValue;

class Connection extends BaseHeaderValue
{
    public const NAME = 'Connection';

    protected const PARSING_LIST = true;
}
