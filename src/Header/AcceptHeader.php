<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header;

use InvalidArgumentException;
use Yiisoft\Http\Header\Value\Accept\Accept;
use Yiisoft\Http\Header\Value\Accept\Base;
use Yiisoft\Http\Header\Value\BaseHeaderValue;

class AcceptHeader extends Header
{
    // todo: sorting, comparing
    protected const DEFAULT_VALUE_CLASS = Accept::class;

    public function __construct(string $nameOrClass) {
        parent::__construct($nameOrClass);
        if (!is_subclass_of($this->headerClass, Base::class, true)) {
            throw new InvalidArgumentException(
                sprintf("%s class is not an instance of %s", $this->headerClass, Base::class)
            );
        }
    }

    /**
     * Add value in order
     */
    protected function addValue(BaseHeaderValue $value): void
    {
        if (count($this->collection) === 0) {
            $this->collection[] = $value;
            return;
        }
        for ($pos = array_key_last($this->collection); $pos >= 0; --$pos) {
            $item = $this->collection[$pos];
            $result = (float)$item->getQuality() <=> (float)$value->getQuality();
            if ($result > 0) {
                break;
            } elseif ($result === 0) {
                $itemTypes = array_reverse(explode('/', $item->getValue()));
                $valueTypes = array_reverse(explode('/', $value->getValue()));
                var_dump($itemTypes, $valueTypes);
                $result = count($itemTypes) <=> count($valueTypes);
                if ($result > 0) {
                    break;
                } elseif ($result === 0) {
                    foreach ($itemTypes as $part => $itemType) {
                        if ($itemType === '*' xor $valueTypes[$part] === '*') {
                            if ($itemType !== '*') {
                                break 2;
                            } else {
                                $this->collection[$pos + 1] = $item;
                                continue 2;
                            }
                        }
                    }
                    if (count($item->getParams()) >= count($value->getParams())) {
                        break;
                    }
                }
            }
            $this->collection[$pos + 1] = $item;
        }
        $this->collection[$pos + 1] = $value;
    }
}
