<?php

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Rule\ListedValues;
use Yiisoft\Http\Header\Value\BaseHeaderValue;

final class ListedValuesHeaderValue extends BaseHeaderValue implements ListedValues
{
    public const NAME = 'Test-Listed';
}
