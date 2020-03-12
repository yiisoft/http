<?php

namespace Yiisoft\Http\Tests\Header\Value;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Value\Date;

class DateTest extends TestCase
{
    public function testWithValueFromDatetimeImmutability()
    {
        $value = new Date();
        $date = new \DateTimeImmutable('2020-01-01 00:00:00 +0000');

        $clone = $value->withValueFromDatetime($date);

        // the default value of the original object has not changed
        $this->assertSame('', $value->getValue());
        // changes applied
        $this->assertSame('Wed, 01 Jan 2020 00:00:00 GMT', $clone->getValue());
        // immutability
        $this->assertSame(get_class($value), get_class($clone));
        $this->assertNotSame($value, $clone);
    }
    public function testToString()
    {
        $dateStr = '2020-01-01 00:00:00 +0000';
        $value = (new Date())->withValue($dateStr);

        $this->assertSame('Wed, 01 Jan 2020 00:00:00 GMT', (string)$value);
    }
    public function testToStringIncorrect()
    {
        $dateStr = 'not a date';
        $value = (new Date())->withValue($dateStr);

        $this->assertTrue($value->hasError());
        $this->assertSame('not a date', (string)$value);
    }
    public function testGetDatetimeValue()
    {
        $dateStr = '2020-01-01 00:00:00 +0000';
        $value = new Date($dateStr);

        $this->assertInstanceOf(\DateTimeImmutable::class, $value->getDatetimeValue());
    }
    public function testConstructWithDatetimeInterface()
    {
        $date = new \DateTime();
        $value = (new Date($date));
        $this->assertInstanceOf(\DateTimeImmutable::class, $value->getDatetimeValue());
    }
    public function testGetDatetimeValueFromEmpty()
    {
        $value = (new Date());
        $this->assertInstanceOf(\DateTimeImmutable::class, $value->getDatetimeValue());
    }
    public function testGetDatetimeValueFromIncorrect()
    {
        $value = (new Date())->withValue('not a date');

        $this->assertTrue($value->hasError());
        $this->assertNull($value->getDatetimeValue());
        $this->assertSame('not a date', (string)$value);
    }
}
