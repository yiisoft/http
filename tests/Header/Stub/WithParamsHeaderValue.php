<?php

namespace Yiisoft\Http\Tests\Header\Stub;

use Yiisoft\Http\Header\HeaderValue;
use Yiisoft\Http\Header\WithParams;

final class WithParamsHeaderValue extends HeaderValue implements WithParams
{
    public const NAME = 'Test-Params';
}
