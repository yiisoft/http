<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Internal\WithParamsHeaderValue;

final class ListedValuesWithParamsHeaderValue extends WithParamsHeaderValue
{
    public const NAME = 'Test-Listed-Params';

    protected const PARSING_LIST = true;
}
