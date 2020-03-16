<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Yiisoft\Http\Header\Rule\ListedValues;

class Connection extends BaseHeaderValue implements ListedValues
{
    public const NAME = 'Connection';
}
