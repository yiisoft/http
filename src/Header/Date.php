<?php

namespace Yiisoft\Http\Header;

use DateTimeImmutable;

final class Date extends HeaderValue
{
    public const NAME = 'Date';

    /**
     * @return DateTimeImmutable
     * @throws \Exception
     */
    public function getDatetimeValue(): DateTimeImmutable
    {
        return new DateTimeImmutable($this->value);
    }
    public function withValueFromDatetime(DateTimeImmutable $date): self
    {
        return $this->withValue($date->format(self::HTTP_DATETIME_FORMAT));
    }
}
