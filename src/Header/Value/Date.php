<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use DateTimeImmutable;

final class Date extends BaseHeaderValue
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
