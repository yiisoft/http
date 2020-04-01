<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header;

use DateTimeInterface;
use Yiisoft\Http\Header\Internal\BaseHeaderValue;
use Yiisoft\Http\Header\Value\Date;

/**
 * @method $this withValue(string|DateTimeInterface|BaseHeaderValue $value)
 * @method $this withValues(string[]|DateTimeInterface[]|BaseHeaderValue[] $value)
 */
final class DateHeader extends Header
{
    protected const DEFAULT_VALUE_CLASS = Date::class;

    protected function addValue($value): void
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(DateTimeInterface::RFC7231);
        }
        parent::addValue($value);
    }
}
