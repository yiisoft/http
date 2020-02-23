<?php

namespace Yiisoft\Http\Tests\Header;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Accept;
use Yiisoft\Http\Header\Date;
use Yiisoft\Http\Header\DefaultHeader;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\HeaderValues;

class HeaderValuesTest extends TestCase
{
    public function testHeaderClassPassed()
    {
        $values = new HeaderValues(Date::class);
        $this->assertSame('Date', $values->getName());
    }
    public function testErrorWithDefaultHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new HeaderValues(DefaultHeader::class);
    }
    public function testErrorWithHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new HeaderValues(Header::class);
    }
    public function testErrorIfNotHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new HeaderValues(\DateTimeImmutable::class);
    }
    public function testCreateFromOneStringValue()
    {
        $headers = ['Newauth realm="apps", type=1, title="Login to \\"apps\\"", Basic realm="simple"'];

        $values = HeaderValues::createFromArray($headers, 'WWW-Authenticate');

        $this->assertSame('WWW-Authenticate', $values->getName());
        $this->assertSame($headers, $values->getValues());
    }
    public function testCreateFromFewStringValues()
    {
        $headers = [
            'text/*;q=0.3',
            'text/html;q=0.7',
            'text/html;level=1',
            'text/html;level=2;q=0.4',
            '*/*;q=0.5',
            'text/*',
            'text/plain',
            'text/plain;format=flowed',
            '*/*',
        ];

        $values = HeaderValues::createFromArray($headers, 'accept');

        $this->assertSame('accept', $values->getName());
        $this->assertSame($headers, $values->getValues());
    }
    public function testAddObject()
    {
        $headers = [
            'text/*;q=0.3',
            'text/html;q=0.7',
        ];
        $values = HeaderValues::createFromArray($headers, Accept::class);

        $values->add(new Accept('*/*'));

        $this->assertSame(Accept::NAME, $values->getName());
        $this->assertSame(['text/*;q=0.3', 'text/html;q=0.7', '*/*'], $values->getValues());
    }
    public function testExceptionWhenAddOtherObject()
    {
        $headers = [
            'text/*;q=0.3',
            'text/html;q=0.7',
        ];
        $values = HeaderValues::createFromArray($headers, Accept::class);

        $this->expectException(InvalidArgumentException::class);

        $values->add(new Date('*/*'));
    }

}
