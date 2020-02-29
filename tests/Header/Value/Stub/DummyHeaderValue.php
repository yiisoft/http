<?php

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Value\BaseHeaderValue;
use Yiisoft\Http\Header\WithParams;

final class DummyHeaderValue extends BaseHeaderValue
{
    public const NAME = 'Test-Dummy';
}
