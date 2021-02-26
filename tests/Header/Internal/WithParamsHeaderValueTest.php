<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header\Internal;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\SortableHeader;
use Yiisoft\Http\Tests\Header\Value\Stub\SortedHeaderValue;
use Yiisoft\Http\Tests\Header\Value\Stub\WithParamsHeaderValue;

class WithParamsHeaderValueTest extends TestCase
{
    public function testCreateHeader()
    {
        $header = WithParamsHeaderValue::createHeader();

        $this->assertInstanceOf(Header::class, $header);
        $this->assertSame(WithParamsHeaderValue::NAME, $header->getName());
    }

    public function testCreateHeaderQ()
    {
        $header = SortedHeaderValue::createHeader();

        $this->assertInstanceOf(SortableHeader::class, $header);
        $this->assertSame(SortedHeaderValue::NAME, $header->getName());
    }

    public function testWithParamsImmutability()
    {
        $value = new WithParamsHeaderValue();

        $clone = $value->withParams([]);

        $this->assertSame(get_class($value), get_class($clone));
        $this->assertNotSame($value, $clone);
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
