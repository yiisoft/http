<?php

namespace Yiisoft\Http\Header;

use DateTimeImmutable;

class Date extends HeaderValue
{
    public const NAME = 'Date';

    public function getParsedValue(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->value);
    }
}
