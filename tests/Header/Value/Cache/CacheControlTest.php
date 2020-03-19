<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header\Value\Cache;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\CacheControlHeader;
use Yiisoft\Http\Header\Value\Cache\CacheControl;

class CacheControlTest extends TestCase
{
    public function testCreateHeader()
    {
        $header = CacheControl::createHeader();
        $this->assertInstanceOf(CacheControlHeader::class, $header);
    }
    public function testWithDirectiveImmutability()
    {
        $origin = new CacheControl();

        $clone = $origin->withDirective('no-cache', null);

        // the default values of the original object have not changed
        $this->assertNull($origin->getDirective());
        $this->assertNull($origin->getArgument());
        // changes applied
        $this->assertSame('no-cache', $clone->getDirective());
        $this->assertNull($clone->getArgument());
        // immutability
        $this->assertSame(get_class($origin), get_class($clone));
        $this->assertNotSame($origin, $clone);
    }
    public function testToStringWithoutArgument()
    {
        $headerValue = (new CacheControl())->withDirective(CacheControl::NO_CACHE);

        $this->assertSame('no-cache', (string)$headerValue);
    }
    public function testToStringNumericArgument()
    {
        $headerValue = (new CacheControl())->withDirective(CacheControl::MAX_AGE, '1560');

        $this->assertSame('max-age=1560', (string)$headerValue);
    }
    public function testToStringEmptyListedArgument()
    {
        $headerValue = (new CacheControl())->withDirective(CacheControl::PRIVATE);

        $this->assertSame('private', (string)$headerValue);
    }
    public function testToStringListedArgument()
    {
        $headerValue = (new CacheControl())->withDirective(CacheControl::PRIVATE, 'etag');

        $this->assertSame('private="etag"', (string)$headerValue);
    }
    public function testCustomDirective()
    {
        $headerValue = (new CacheControl())->withDirective('custom-name', 'custom_value');

        $this->assertSame('custom-name=custom_value', (string)$headerValue);
    }
    public function testToStringEmptyDirective()
    {
        $headerValue = (new CacheControl());

        $this->assertSame('', (string)$headerValue);
    }
    public function testWithValue()
    {
        $headerValue = (new CacheControl())->withValue('test-directive');

        $this->assertSame('test-directive', $headerValue->getDirective());
        $this->assertSame('test-directive', (string)$headerValue);
        $this->assertFalse($headerValue->hasError());
    }
    public function testWithParams()
    {
        $headerValue = (new CacheControl())->withParams(['test-name' => 'test-value']);

        $this->assertSame('test-name', $headerValue->getDirective());
        $this->assertSame('test-value', $headerValue->getArgument());
        $this->assertSame('test-name="test-value"', (string)$headerValue);
        $this->assertFalse($headerValue->hasError());
    }
    public function testWithEmptyParams()
    {
        $headerValue = (new CacheControl())->withParams([]);

        $this->assertSame(null, $headerValue->getDirective());
        $this->assertSame(null, $headerValue->getArgument());
        $this->assertSame('', (string)$headerValue);
        $this->assertFalse($headerValue->hasError());
    }
    public function testWithMultipleParams()
    {
        $headerValue = (new CacheControl())->withParams(['test-name' => 'test-value', 'foo' => 'bar']);

        $this->assertSame('test-name', $headerValue->getDirective());
        $this->assertSame('test-value', $headerValue->getArgument());
        $this->assertSame('test-name="test-value"', (string)$headerValue);
        $this->assertFalse($headerValue->hasError());
    }
    public function testWithValueRewritesByWithParams()
    {
        $headerValue = (new CacheControl())->withValue('test-directive')->withParams(['test-name' => 'test-value']);

        $this->assertSame('test-name', $headerValue->getDirective());
        $this->assertSame('test-value', $headerValue->getArgument());
        $this->assertSame('test-name="test-value"', (string)$headerValue);
        $this->assertFalse($headerValue->hasError());
    }

    public function withDirectiveDataProvider(): array
    {
        return [
            'nullable-argument' => ['no-store', null, 'no-store', null, 'no-store'],
            'case-1' => ['No-CAche', null, 'no-cache', null, 'no-cache'],
            'case-2' => ['No-CAche', 'eTaG', 'no-cache', 'eTaG', 'no-cache="eTaG"'],
            'case-3' => ['test-DIrective', 'CaSe', 'test-directive', 'CaSe', 'test-directive=CaSe'],
            'case-4' => ['test-DIrective', 'Ca Se', 'test-directive', 'Ca Se', 'test-directive="Ca Se"'],
            'ext-directive-case' => ['Test-directive', null, 'test-directive', null, 'test-directive'],
            'ext-directive-argument' => ['test-directive', 'null', 'test-directive', 'null', 'test-directive=null'],
            'ext-directive-arg-char-0' => ['test-directive', '', 'test-directive', '', 'test-directive=""'],
            'ext-directive-arg-char-1' => ['test-directive', ' ', 'test-directive', ' ', 'test-directive=" "'],
            'ext-directive-arg-char-2' => ['test-directive', '"', 'test-directive', '"', 'test-directive="\\""'],
            'ext-directive-arg-char-3' => ['test-directive', '\\', 'test-directive', '\\', 'test-directive="\\\\"'],
            'ext-directive-arg-char-4' => ['test-directive', '-', 'test-directive', '-', 'test-directive="-"'],
            'double-type-1' => ['no-cache', null, 'no-cache', null, 'no-cache'],
            'double-type-2' => ['no-cache', 'Content-Length, ETag', 'no-cache', 'Content-Length, ETag', 'no-cache="Content-Length, ETag"'],
            'numeric-arg-0' => ['max-age', '0', 'max-age', '0', 'max-age=0'],
            'numeric-arg-1' => ['max-age', '123', 'max-age', '123', 'max-age=123'],
            'numeric-arg-2' => ['max-age', '0123', 'max-age', '0123', 'max-age=0123'],
            'header-list-arg-1' => ['private', 'test', 'private', 'test', 'private="test"'],
            'header-list-arg-2' => ['private', 'test , test', 'private', 'test , test', 'private="test , test"'],
            'header-list-arg-3' => ['private', '  test , test  ', 'private', 'test , test', 'private="test , test"'],
        ];
    }
    /**
     * @dataProvider withDirectiveDataProvider
     */
    public function testWithDirectiveCorrect(
        string $inputDirective,
        ?string $imputArgument,
        ?string $directive,
        ?string $argument,
        string $output
    ) {
        $headerValue = (new CacheControl())->withDirective($inputDirective, $imputArgument);

        $this->assertFalse($headerValue->hasError());
        $this->assertSame($directive, $headerValue->getDirective());
        $this->assertSame($argument, $headerValue->getArgument());
        $this->assertSame($output, (string)$headerValue);
    }

    public function withDirectiveIncorrectDataProvider(): array
    {
        return [
            'header-list-arg-0' => ['private', '!'],
            'header-list-arg-1' => ['private', ''],
            'header-list-arg-2' => ['private', ' '],
            'header-list-arg-3' => ['private', ',,'],
            'header-list-arg-4' => ['private', 'test , , test'],
            'header-list-arg-5' => ['private', 'ETag,'],
            'numeric-arg-0' => ['max-age', null],
            'numeric-arg-1' => ['max-age', 'test'],
            'numeric-arg-2' => ['max-age', '123test'],
            'numeric-arg-3' => ['max-age', '0x123'],
            'numeric-arg-4' => ['max-age', '-123'],
            'numeric-arg-5' => ['max-age', '12 34'],
            'argument-should-be-empty' => ['No-Store', 'null'],
        ];
    }
    /**
     * @dataProvider withDirectiveIncorrectDataProvider
     */
    public function testWithDirectiveIncorrectCases(
        string $inputDirective,
        ?string $imputArgument
    ) {
        $this->expectException(\InvalidArgumentException::class);
        (new CacheControl())->withDirective($inputDirective, $imputArgument);
    }
}
