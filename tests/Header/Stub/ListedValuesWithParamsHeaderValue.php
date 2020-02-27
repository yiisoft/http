<?php

namespace Yiisoft\Http\Tests\Header\Stub;

use Yiisoft\Http\Header\HeaderValue;
use Yiisoft\Http\Header\ListedValues;
use Yiisoft\Http\Header\WithParams;

final class ListedValuesWithParamsHeaderValue extends HeaderValue implements ListedValues, WithParams
{
    public const NAME = 'Test-Listed-Params';
}
