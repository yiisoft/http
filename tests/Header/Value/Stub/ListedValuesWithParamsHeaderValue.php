<?php

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Rule\ListedValues;
use Yiisoft\Http\Header\Rule\WithParams;
use Yiisoft\Http\Header\Value\BaseHeaderValue;

final class ListedValuesWithParamsHeaderValue extends BaseHeaderValue implements ListedValues, WithParams
{
    public const NAME = 'Test-Listed-Params';
}
