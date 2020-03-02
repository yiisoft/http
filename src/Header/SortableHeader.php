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
            throw new InvalidArgumentException(
                sprintf("%s class does not implement %s", $this->headerClass, WithQualityParam::class)
            );
        }
    }

    /**
     * Todo
     * Add and and sort value
     */
    protected function addValue(BaseHeaderValue $value): void
    {
        if (count($this->collection) === 0) {
            $this->collection[] = $value;
            return;
        }
        for ($pos = array_key_last($this->collection); $pos >= 0; --$pos) {
            $item = $this->collection[$pos];
            if ((float)$item->getQuality() >= (float)$value->getQuality()) {
                break;
            }
        }
        if ($pos < 0) {
            array_unshift($this->collection, $value);
        } elseif ($pos === array_key_last($this->collection)) {
            $this->collection[] = $value;
        } else {
            $toPush = array_slice($this->collection, $pos + 1);
            $this->collection = array_slice($this->collection, 0, $pos + 1);
            array_push($this->collection, $value, ...$toPush);
        }
    }
}
