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
        $dateStr = 'Friday, 04-Jul-08 08:42:36 GMT';
        $value = (new Date())->withValue($dateStr);

        $this->assertSame('Fri, 04 Jul 2008 08:42:36 GMT', (string)$value);
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
        $dateStr = 'Fri Jul 4 08:42:36 2008';
        $value = new Date($dateStr);

        $this->assertInstanceOf(\DateTimeImmutable::class, $value->getDatetimeValue());
    }
    public function testRFC7231()
    {
        $dateStr = 'Fri, 04 Jul 2008 08:42:36 GMT';
        $value = new Date($dateStr);

        $this->assertInstanceOf(\DateTimeImmutable::class, $value->getDatetimeValue());
        $this->assertSame('Fri, 04 Jul 2008 08:42:36 GMT', (string)$value);
    }
    public function testRFC850()
    {
        $dateStr = 'Friday, 04-Jul-08 08:42:36 GMT';
        $value = new Date($dateStr);

        $this->assertInstanceOf(\DateTimeImmutable::class, $value->getDatetimeValue());
        $this->assertSame('Fri, 04 Jul 2008 08:42:36 GMT', (string)$value);
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
    public function testGetDatetimeValueFromIncorrectForHttp()
    {
        $value = (new Date())->withValue('2008-07-12 10:15');

        $this->assertTrue($value->hasError());
        $this->assertNull($value->getDatetimeValue());
        $this->assertSame('2008-07-12 10:15', (string)$value);
    }
}
