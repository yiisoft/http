<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Date;

use DateTimeImmutable;
use Exception;
use Yiisoft\Http\Header\Value\BaseHeaderValue;

class Date extends BaseHeaderValue
{
    public const NAME = 'Date';

    protected const HTTP_DATETIME_FORMAT = 'D, d M Y H:i:s \\G\\M\\T';

    final public function getDatetimeValue(): ?DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($this->value);
        } catch (Exception $e) {
            $this->error = $e;
            return null;
        }
    }

    final public function withValueFromDatetime(DateTimeImmutable $date): self
    {
        return $this->withValue($date->format(self::HTTP_DATETIME_FORMAT));
    }
}
