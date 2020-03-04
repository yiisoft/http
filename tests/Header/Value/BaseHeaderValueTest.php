<?php

namespace Yiisoft\Http\Tests\Header\Value;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Tests\Header\Value\Stub\DummyHeaderValue;
use Yiisoft\Http\Tests\Header\Value\Stub\SortedHeaderValue;
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
    public function testBehaviorWithoutParams()
    {
        $params = ['q' => '0.4', 'param' => 'test', 'foo' => 'bar'];
        $value = (new DummyHeaderValue('foo'))
            ->withParams($params);

        $this->assertFalse($value->hasError());
        $this->assertSame('foo', (string)$value);
        $this->assertSame('1', $value->getQuality());
        $this->assertEquals($params, $value->getParams());
    }
    public function testBehaviorWithParams()
    {
        $params = ['q' => '0.4', 'param' => 'test', 'foo' => 'bar'];
        $value = (new WithParamsHeaderValue('foo'))
            ->withParams($params);

        $this->assertFalse($value->hasError());
        $this->assertSame('foo;q=0.4;param=test;foo=bar', (string)$value);
        $this->assertSame('1', $value->getQuality());
        $this->assertEquals($params, $value->getParams());
    }
    public function testBehaviorWithQualityParam()
    {
        $params = ['param' => 'test', 'foo' => 'bar', 'q' => '0.4'];
        $value = (new SortedHeaderValue('foo'))
            ->withParams($params);

        $this->assertFalse($value->hasError());
        $this->assertSame('foo;param=test;foo=bar;q=0.4', (string)$value);
        $this->assertSame('0.4', $value->getQuality());
        $this->assertEquals(['q' => '0.4', 'param' => 'test', 'foo' => 'bar'], $value->getParams());
    }
}
