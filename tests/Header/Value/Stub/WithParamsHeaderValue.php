<?php

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Rule\WithParams;
use Yiisoft\Http\Header\Value\BaseHeaderValue;

final class WithParamsHeaderValue extends BaseHeaderValue implements WithParams
{
    public const NAME = 'Test-Params';
}
