<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Cache;

use Yiisoft\Http\Header\ParsingException;
use Yiisoft\Http\Header\Internal\BaseHeaderValue;

/**
 * @link https://tools.ietf.org/html/rfc7234#section-5.1
 */
final class Age extends BaseHeaderValue
{
    public const NAME = 'Age';

    /**
     * @param int|string $value
     * @return BaseHeaderValue
     */
    public function withValue($value): BaseHeaderValue
    {
        if (is_int($value)) {
            $value = (string)$value;
        }
        return parent::withValue($value);
    }

    protected function setValue(string $value): void
    {
        if (preg_match('/^\\d+$/', $value) !== 1) {
            $this->error = new ParsingException($value, 0, 'The value must consist of digits only.');
        } else {
            $this->error = null;
        }
        parent::setValue($value);
    }
}
