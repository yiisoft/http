<?php

namespace Yiisoft\Http\Tests\Header;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\SortableHeader;
use Yiisoft\Http\Tests\Header\Value\Stub\DummyHeaderValue;
use Yiisoft\Http\Tests\Header\Value\Stub\SortedHeaderValue;

class SortableHeaderTest extends TestCase
{
    public function testHeaderValueClassPassed()
    {
        $values = new SortableHeader(SortedHeaderValue::class);
        $this->assertSame('Test-Quality', $values->getName());
        $this->assertSame(SortedHeaderValue::class, $values->getValueClass());
    }
    public function testErrorWithHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new SortableHeader(DummyHeaderValue::class);
    }
    public function testCreateFromStringValues()
    {
        $header = (new SortableHeader('Accept'))->withValue('*/*');

        $this->assertSame('Accept', $header->getName());
        $this->assertSame(['*/*'], $header->getStrings());
    }
    public function testCreateFromFewStringValues()
    {
        $headers = [
            'text/*;q=0.3',
            'text/html',
            '*/*;q=0.001',
            'text/plain;q=0.5',
        ];

        $header = (new SortableHeader('Accept-Test'))->withValues($headers);

        $this->assertSame('Accept-Test', $header->getName());
        $this->assertSame(
            [
                'text/html',
                'text/plain;q=0.5',
                'text/*;q=0.3',
                '*/*;q=0.001',
            ],
            $header->getStrings()
        );
    }
    public function testCreateFromManyStringValues()
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

        $header = (new SortableHeader('Accept'))->withValues($headers);

        $this->assertSame('Accept', $header->getName());
        $this->assertSame(
            [
                'text/html;level=1',
                'text/*',
                'text/plain',
                'text/plain;format=flowed',
                '*/*',
                'text/html;q=0.7',
                '*/*;q=0.5',
                'text/html;level=2;q=0.4',
                'text/*;q=0.3',
            ],
            $header->getStrings()
        );
    }
}
