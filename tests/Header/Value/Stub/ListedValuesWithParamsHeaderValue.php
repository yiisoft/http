<?php

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Value\BaseHeaderValue;
use Yiisoft\Http\Header\ListedValues;
use Yiisoft\Http\Header\WithParams;

final class ListedValuesWithParamsHeaderValue extends BaseHeaderValue implements ListedValues, WithParams
{
    public const NAME = 'Test-Listed-Params';
}
