<?php

namespace Yiisoft\Http\Tests\Header\Internal;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Tests\Header\Value\Stub\DummyHeaderValue;

class BaseHeaderValueTest extends TestCase
{
    public function testWithValueImmutability()
    {
        $value = new DummyHeaderValue();

        $clone = $value->withValue('test');

        // the default value of the original object has not changed
        $this->assertSame('', $value->getValue());
        // changes applied
        $this->assertSame('test', $clone->getValue());
        // immutability
        $this->assertSame(get_class($value), get_class($clone));
        $this->assertNotSame($value, $clone);
    }
    public function testWithErrorImmutability()
    {
        $value = new DummyHeaderValue();

        $clone = $value->withError(null);

        $this->assertSame(get_class($value), get_class($clone));
        $this->assertNotSame($value, $clone);
    }
    public function testCreateHeader()
    {
        $header = DummyHeaderValue::createHeader();

        $this->assertInstanceOf(Header::class, $header);
        $this->assertSame(DummyHeaderValue::NAME, $header->getName());
    }
}
