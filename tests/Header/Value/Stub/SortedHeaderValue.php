<?php

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Rule\WithQualityParam;

final class SortedHeaderValue extends \Yiisoft\Http\Header\Value\BaseHeaderValue implements WithQualityParam
{
    public const NAME = 'Test-Quality';
    public function setQuality(string $q): bool
    {
        return parent::setQuality($q);
    }
}
