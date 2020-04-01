<?php

namespace Yiisoft\Http\Tests\Header\Internal;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\DirectiveHeader;
use Yiisoft\Http\Tests\Header\Value\Stub\DirectivesHeaderValue;

class DirectivesHeaderValueTest extends TestCase
{
    public function testWithDirectiveImmutability()
    {
        $origin = new DirectivesHeaderValue();

        $clone = $origin->withDirective('test');

        // the default value of the original object has not changed
        $this->assertSame('', $origin->getDirective());
        $this->assertNull($origin->getArgument());
        // changes applied
        $this->assertSame('test', $clone->getDirective());
        $this->assertNull($clone->getArgument());
        // immutability
        $this->assertSame(get_class($origin), get_class($clone));
        $this->assertNotSame($origin, $clone);
    }
    public function testWithDirectiveArg()
    {
        $value = (new DirectivesHeaderValue())
            ->withDirective('foo', 'bar');


        $this->assertFalse($value->hasError());
        $this->assertSame('foo=bar', (string)$value);
        $this->assertSame('foo', $value->getDirective());
        $this->assertSame('bar', $value->getArgument());
    }
    public function testCreateHeader()
    {
        $header = DirectivesHeaderValue::createHeader();

        $this->assertInstanceOf(DirectiveHeader::class, $header);
        $this->assertSame(DirectivesHeaderValue::NAME, $header->getName());
    }
    public function testToStringWithoutArgument()
    {
        $headerValue = (new DirectivesHeaderValue())->withDirective(DirectivesHeaderValue::EMPTY);

        $this->assertSame('empty', (string)$headerValue);
    }
    public function testToStringNumericArgument()
    {
        $headerValue = (new DirectivesHeaderValue())->withDirective(DirectivesHeaderValue::NUMERIC, '1560');

        $this->assertSame('numeric=1560', (string)$headerValue);
    }
    public function testToStringEmptyListedArgument()
    {
        $headerValue = (new DirectivesHeaderValue())->withDirective(DirectivesHeaderValue::LIST_OR_EMPTY);

        $this->assertSame('list-or-empty', (string)$headerValue);
    }
    public function testToStringListedArgument()
    {
        $headerValue = (new DirectivesHeaderValue())->withDirective(DirectivesHeaderValue::HEADER_LIST, 'etag');

        $this->assertSame('header-list="etag"', (string)$headerValue);
    }

    public function testCustomDirective()
    {
        $headerValue = (new DirectivesHeaderValue())->withDirective('custom-directive-name', 'custom_value');

        $this->assertSame('custom-directive-name=custom_value', (string)$headerValue);
    }
    public function testToStringEmptyDirective()
    {
        $headerValue = (new DirectivesHeaderValue());

        $this->assertSame('', (string)$headerValue);
    }
    public function testWithValue()
    {
        $headerValue = (new DirectivesHeaderValue())->withValue('test-value-to-directive');

        $this->assertSame('test-value-to-directive', $headerValue->getValue());
        $this->assertSame('test-value-to-directive', $headerValue->getDirective());
        $this->assertSame('test-value-to-directive', (string)$headerValue);
        $this->assertFalse($headerValue->hasError());
    }

    public function withDirectiveDataProvider(): array
    {
        return [
            'nullable-argument' => ['empty', null, 'empty', null, 'empty'],
            'case-1' => ['emPTy', null, 'empty', null, 'empty'],
            'case-2' => ['hEAder-LIst', 'eTaG', 'header-list', 'eTaG', 'header-list="eTaG"'],
            'case-3' => ['test-DIrective', 'CaSe', 'test-directive', 'CaSe', 'test-directive=CaSe'],
            'case-4' => ['test-DIrective', 'Ca Se', 'test-directive', 'Ca Se', 'test-directive="Ca Se"'],
            'ext-directive-case' => ['Test-directive', null, 'test-directive', null, 'test-directive'],
            'ext-directive-argument' => ['test-directive', 'null', 'test-directive', 'null', 'test-directive=null'],
            'ext-directive-arg-char-0' => ['test-directive', '', 'test-directive', '', 'test-directive=""'],
            'ext-directive-arg-char-1' => ['test-directive', ' ', 'test-directive', ' ', 'test-directive=" "'],
            'ext-directive-arg-char-2' => ['test-directive', '"', 'test-directive', '"', 'test-directive="\\""'],
            'ext-directive-arg-char-3' => ['test-directive', '\\', 'test-directive', '\\', 'test-directive="\\\\"'],
            'ext-directive-arg-char-4' => ['test-directive', '-', 'test-directive', '-', 'test-directive="-"'],
            'double-type-1' => ['list-or-empty', null, 'list-or-empty', null, 'list-or-empty'],
            'double-type-2' => [
                'list-or-empty',
                'Content-Length, ETag',
                'list-or-empty',
                'Content-Length, ETag',
                'list-or-empty="Content-Length, ETag"',
            ],
            'numeric-arg-0' => ['numeric', '0', 'numeric', '0', 'numeric=0'],
            'numeric-arg-1' => ['numeric', '123', 'numeric', '123', 'numeric=123'],
            'numeric-arg-2' => ['numeric', '0123', 'numeric', '0123', 'numeric=0123'],
            'header-list-arg-1' => ['header-list', 'test', 'header-list', 'test', 'header-list="test"'],
            'header-list-arg-2' => [
                'header-list',
                'test , test',
                'header-list',
                'test , test',
                'header-list="test , test"',
            ],
            'header-list-arg-3' => [
                'header-list',
                '  test , test  ',
                'header-list',
                'test , test',
                'header-list="test , test"',
            ],
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
        $headerValue = (new DirectivesHeaderValue())->withDirective($inputDirective, $imputArgument);

        $this->assertFalse($headerValue->hasError());
        $this->assertSame($directive, $headerValue->getDirective());
        $this->assertSame($argument, $headerValue->getArgument());
        $this->assertSame($output, (string)$headerValue);
    }

    public function withDirectiveIncorrectDataProvider(): array
    {
        return [
            'header-list-arg-0' => ['header-list', '!'],
            'header-list-arg-1' => ['header-list', ''],
            'header-list-arg-2' => ['header-list', ' '],
            'header-list-arg-3' => ['header-list', ',,'],
            'header-list-arg-4' => ['header-list', 'test , , test'],
            'header-list-arg-5' => ['header-list', 'ETag,'],
            'numeric-arg-0' => ['numeric', null],
            'numeric-arg-1' => ['numeric', 'test'],
            'numeric-arg-2' => ['numeric', '123test'],
            'numeric-arg-3' => ['numeric', '0x123'],
            'numeric-arg-4' => ['numeric', '-123'],
            'numeric-arg-5' => ['numeric', '12 34'],
            'argument-should-be-empty' => ['empty', 'null'],
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
        (new DirectivesHeaderValue())->withDirective($inputDirective, $imputArgument);
    }
}
