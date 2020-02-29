<?php

namespace Yiisoft\Http\Tests\Header\Value;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Tests\Header\Value\Stub\DummyHeaderValue;
use Yiisoft\Http\Tests\Header\Value\Stub\WithParamsHeaderValue;

class BaseHeaderValueTest extends TestCase
{
    public function testWithValueImmutability()
    {
        $value = new DummyHeaderValue();

        $clone = $value->withValue('test');

        $this->assertSame(get_class($value), get_class($clone));
        $this->assertNotSame($value, $clone);
    }
    public function testWithParamsImmutability()
    {
        $value = new WithParamsHeaderValue();

        $clone = $value->withParams([]);

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
