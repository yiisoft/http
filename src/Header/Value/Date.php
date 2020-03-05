<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Yiisoft\Http\Header\DateHeader;

class Date extends BaseHeaderValue
{
    public const NAME = 'Date';

    /**
     * Date constructor.
     * @param DateTimeInterface|string $value
     */
    public function __construct($value = '')
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(DateTimeInterface::RFC7231);
        }
        parent::__construct($value);
    }

    final public static function createHeader(): DateHeader
    {
        return new DateHeader(static::class);
    }

    final public function getDatetimeValue(): ?DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($this->value);
        } catch (Exception $e) {
            $this->error = $e;
            return null;
        }
    }

    final public function withValueFromDatetime(DateTimeInterface $date): self
    {
        return $this->withValue($date->format(DateTimeInterface::RFC7231));
    }
}
