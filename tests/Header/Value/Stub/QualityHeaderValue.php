<?php

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\WithQualityParam;

final class QualityHeaderValue extends \Yiisoft\Http\Header\Value\BaseHeaderValue implements WithQualityParam
{
    public const NAME = 'Test-Quality';
    public function setQuality(string $q): bool
    {
        return parent::setQuality($q);
    }
}
