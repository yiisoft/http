<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header;

use InvalidArgumentException;
use Yiisoft\Http\Header\Value\BaseHeaderValue;
use Yiisoft\Http\Header\Value\SortedValue;

final class SortableHeader extends Header
{
    protected const DEFAULT_VALUE_CLASS = SortedValue::class;

    public function __construct(string $nameOrClass)
    {
        parent::__construct($nameOrClass);
        if (!is_subclass_of($this->headerClass, WithQualityParam::class, true)) {
            throw new InvalidArgumentException(
                sprintf("%s class does not implement %s", $this->headerClass, WithQualityParam::class)
            );
        }
    }

    /**
     * Add value in order
     */
    protected function collect(BaseHeaderValue $value): void
    {
        if (count($this->collection) === 0) {
            $this->collection[] = $value;
            return;
        }
        for ($pos = array_key_last($this->collection); $pos >= 0; --$pos) {
            $item = $this->collection[$pos];
            $result = (float)$item->getQuality() <=> (float)$value->getQuality();
            if ($result >= 0) {
                $this->collection[$pos + 1] = $value;
                return;
            }
            $this->collection[$pos + 1] = $item;
        }
        $this->collection[0] = $value;
    }
}
