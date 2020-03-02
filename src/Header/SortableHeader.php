<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header;

use InvalidArgumentException;
use Yiisoft\Http\Header\Value\BaseHeaderValue;
use Yiisoft\Http\Header\Value\SortableValue;

class SortableHeader extends Header
{
    // todo: sorting, comparing
    protected const DEFAULT_VALUE_CLASS = SortableValue::class;

    public function __construct(string $nameOrClass) {
        parent::__construct($nameOrClass);
        if (!is_subclass_of($this->headerClass, WithQualityParam::class, true)) {
            throw new InvalidArgumentException(sprintf("should implement %s", WithQualityParam::class));
        }
    }

    /**
     * Todo
     * Add and sort value
     */
    protected function addValue(BaseHeaderValue $value): void
    {
        parent::addValue($value);
    }
}
