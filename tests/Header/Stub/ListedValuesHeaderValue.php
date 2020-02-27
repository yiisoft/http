<?php

namespace Yiisoft\Http\Tests\Header\Stub;

use Yiisoft\Http\Header\HeaderValue;
use Yiisoft\Http\Header\ListedValues;
use Yiisoft\Http\Header\WithParams;

final class ListedValuesHeaderValue extends HeaderValue implements ListedValues
{
    public const NAME = 'Test-Listed';
}
