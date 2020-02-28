<?php

namespace Yiisoft\Http\Tests\Header;

use Exception;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Tests\Header\Stub\DummyHeaderValue;
use Yiisoft\Http\Tests\Header\Stub\WithParamsHeaderValue;

class HeaderValueTest extends TestCase
{
    public function testWithValueReturnsImmutability()
    {
        $value = new DummyHeaderValue();

        $clone = $value->withValue('test');

        $this->assertSame(get_class($value), get_class($clone));
        $this->assertNotSame($value, $clone);
    }
    public function testWithParamsReturnsImmutability()
    {
        $value = new WithParamsHeaderValue();

        $clone = $value->withParams([]);

        $this->assertSame(get_class($value), get_class($clone));
        $this->assertNotSame($value, $clone);
    }
    public function testWithErrorReturnsImmutability()
    {
        $value = new DummyHeaderValue();

        $clone = $value->withError(null);

        $this->assertSame(get_class($value), get_class($clone));
        $this->assertNotSame($value, $clone);
    }
}
