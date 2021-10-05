<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Internal\BaseHeaderValue;

final class DummyHeaderValue extends BaseHeaderValue
{
    public const NAME = 'Test-Dummy';
}
