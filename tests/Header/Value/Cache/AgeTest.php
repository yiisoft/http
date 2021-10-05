<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header\Value\Cache;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Value\Cache\Age;

class AgeTest extends TestCase
{
    public function testWithValueIntegerZero()
    {
        $value = (new Age())->withValue(0);

        $this->assertFalse($value->hasError());
        $this->assertSame('0', $value->getValue());
    }

    public function testWithValueMaxInteger()
    {
        $value = (new Age())->withValue(PHP_INT_MAX);

        $this->assertFalse($value->hasError());
        $this->assertSame((string) PHP_INT_MAX, $value->getValue());
    }

    public function testWithValueMinInteger()
    {
        $value = (new Age())->withValue(PHP_INT_MIN);

        $this->assertTrue($value->hasError());
    }

    public function withValueCorrectDataProvider(): array
    {
        return [
            ['0'],
            ['1'],
            ['000001'],
            ['123456'],
        ];
    }

    /**
     * @dataProvider withValueCorrectDataProvider
     */
    public function testWithValueCorrectCases($input)
    {
        $value = (new Age())->withValue($input);

        $this->assertFalse($value->hasError());
        $this->assertSame($input, $value->getValue());
    }

    public function withValueIncorrectDataProvider(): array
    {
        return [
            'empty' => [''],
            'space' => [' '],
            'signed-1' => ['-1'],
            'signed-2' => ['+1'],
            'word' => ['hello'],
            'words' => ['hello word'],
            'numbers' => ['1 2'],
            'hex-1' => ['0xff'],
            'hex-2' => ['12ab'],
            'hex-3' => ['12AB'],
            'exp' => ['3e5'],
        ];
    }

    /**
     * @dataProvider withValueIncorrectDataProvider
     */
    public function testWithValueIncorrectCases($input)
    {
        $value = (new Age())->withValue($input);

        $this->assertTrue($value->hasError());
        $this->assertSame($input, $value->getValue());
    }
}
