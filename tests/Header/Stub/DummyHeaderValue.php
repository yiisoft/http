<?php

namespace Yiisoft\Http\Tests\Header\Stub;

use Yiisoft\Http\Header\HeaderValue;
use Yiisoft\Http\Header\WithParams;

final class DummyHeaderValue extends HeaderValue
{
    public const NAME = 'Test-Dummy';
}
