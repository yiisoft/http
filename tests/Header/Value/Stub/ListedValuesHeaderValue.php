<?php

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Internal\BaseHeaderValue;

final class ListedValuesHeaderValue extends BaseHeaderValue
{
    public const NAME = 'Test-Listed';

    protected const PARSING_LIST = true;
}
