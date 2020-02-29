<?php

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Value\BaseHeaderValue;
use Yiisoft\Http\Header\WithParams;

final class WithParamsHeaderValue extends BaseHeaderValue implements WithParams
{
    public const NAME = 'Test-Params';
}
