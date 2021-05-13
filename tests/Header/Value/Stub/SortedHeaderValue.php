<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header\Value\Stub;

use Yiisoft\Http\Header\Internal\WithParamsHeaderValue;

final class SortedHeaderValue extends WithParamsHeaderValue
{
    public const NAME = 'Test-Quality';

    protected const PARSING_LIST = true;
    protected const PARSING_Q_PARAM = true;

    public function setQuality(string $q): bool
    {
        return parent::setQuality($q);
    }
}
