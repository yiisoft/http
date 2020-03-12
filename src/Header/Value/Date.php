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

    private ?DateTimeImmutable $datetimeObject = null;

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

    public function __toString(): string
    {
        return $this->datetimeObject === null
            ? parent::__toString()
            : $this->datetimeObject->format(DateTimeInterface::RFC7231);
    }

    final public static function createHeader(): DateHeader
    {
        return new DateHeader(static::class);
    }

    final public function getDatetimeValue(): ?DateTimeImmutable
    {
        return $this->datetimeObject;
    }

    final public function withValueFromDatetime(DateTimeInterface $date): self
    {
        return $this->withValue($date->format(DateTimeInterface::RFC7231));
    }

    final protected function setValue(string $value): void
    {
        try {
            $this->datetimeObject = new DateTimeImmutable($value);
        } catch (Exception $e) {
            $this->datetimeObject = null;
            $this->error = $e;
        }
        parent::setValue($value);
    }
}
