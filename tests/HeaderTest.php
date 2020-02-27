<?php

namespace Yiisoft\Http\Tests\Header;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Accept;
use Yiisoft\Http\Header\Date;
use Yiisoft\Http\Header\HeaderValue;
use Yiisoft\Http\Header;
use Yiisoft\Http\Tests\Header\Stub\WithParamsHeaderValue;

class HeaderTest extends TestCase
{
    public function testHeaderClassPassed()
    {
        $values = new Header(Date::class);
        $this->assertSame('Date', $values->getName());
    }
    public function testErrorWithDefaultHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new Header(HeaderValue::class);
    }
    public function testErrorWithHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new Header(HeaderValue::class);
    }
    public function testErrorIfNotHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new Header(\DateTimeImmutable::class);
    }
    public function testCreateFromOneStringValue()
    {
        $headers = ['Newauth realm="apps", type=1, title="Login to \\"apps\\"", Basic realm="simple"'];

        $values = Header::createFromArray($headers, 'WWW-Authenticate');

        $this->assertSame('WWW-Authenticate', $values->getName());
        $this->assertSame($headers, $values->getStrings());
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

        $values = Header::createFromArray($headers, 'accept');

        $this->assertSame('accept', $values->getName());
        $this->assertSame($headers, $values->getStrings());
    }
    public function testAddObject()
    {
        $headers = [
            'text/*;q=0.3',
            'text/html;q=0.7',
        ];
        $values = Header::createFromArray($headers, Accept::class);

        $values->add(new Accept('*/*'));

        $this->assertSame(Accept::NAME, $values->getName());
        $this->assertSame(['text/*;q=0.3', 'text/html;q=0.7', '*/*'], $values->getStrings());
    }
    public function testExceptionWhenAddOtherObject()
    {
        $headers = [
            'text/*;q=0.3',
            'text/html;q=0.7',
        ];
        $values = Header::createFromArray($headers, Accept::class);

        $this->expectException(InvalidArgumentException::class);

        $values->add(new Date('*/*'));
    }

    public function valueAndParametersDataProvider(): array
    {
        return [
            'empty' => ['', '', []],
            'noParams' => ['test', 'test', []],
            'withParams' => ['test;q=1.0;version=2', 'test', ['q' => '1.0', 'version' => '2']],
            'simple1' => ['audio/*;q=0.2', 'audio/*', ['q' => '0.2']],
            'simple2' => ['gzip;q=1.0', 'gzip', ['q' => '1.0']],
            'simple3' => ['identity;q=0.5', 'identity', ['q' => '0.5']],
            'simple4' => ['*;q=0', '*', ['q' => '0']],
            'quotedParameter' => [
                'test;noquote=test;quote="test2"',
                'test',
                ['noquote' => 'test', 'quote' => 'test2'],
                'test;noquote=test;quote=test2',
            ],
            'quotedEmptyParameter' => ['quote=""', '', ['quote' => '']],
            'singleQuoted' => ["a='a'", '', ['a' => "'a'"]],
            'mixedQuotes' => [
                'a="tes\'t";sp=" s p ";test="\'test\'";test2="\\"quoted\\" test"',
                '',
                ['a' => 'tes\'t', 'sp' => ' s p ', 'test' => '\'test\'', 'test2' => '"quoted" test'],
                'a=tes\'t;sp=" s p ";test=\'test\';test2="\\"quoted\\" test"',
            ],
            'slashes' => [
                'a="\\t\\e\\s\\t";b="te\\\\st";c="\\"\\"',
                '',
                ['a' => 'test', 'b' => 'te\\st', 'c' => '""'],
                'a=test;b="te\\\\st";c="\\"\\""',
            ],
            'specChars1' => ['*|*/*\\*;*=test;test=*', '*|*/*\\*', ['*' => 'test', 'test' => '*']],
            'numbers' => ['123.45;a=8888;b="999"', '123.45', ['a' => '8888', 'b' => '999'], '123.45;a=8888;b=999'],
            'specChars2' => ['param*1=a;param*2=b', '', ['param*1' => 'a', 'param*2' => 'b']],

            'withSpacesAfterDelimiters' => [
                'test; q=1.0; version=2',
                'test',
                ['q' => '1.0', 'version' => '2'],
                'test;q=1.0;version=2',
            ],
            'spacesAroundDelimiters' => [
                'test ; q=1.0 ; version=2',
                'test',
                ['q' => '1.0', 'version' => '2'],
                'test;q=1.0;version=2',
            ],
            'emptyValueWithParam' => ['param=value', '', ['param' => 'value']],
            'emptyValueWithParam2' => ['param=value;a=b', '', ['param' => 'value', 'a' => 'b']],
            'missingDelimiterBtwValueAndParam' => ['value   a=a1', 'value', ['a' => 'a1'], 'value;a=a1'],
            'case' => ['VaLue;A=TEST;TEST=B', 'VaLue', ['a' => 'TEST', 'test' => 'B'], 'VaLue;a=TEST;test=B'],
            'percent' => [
                '%12%34%;a=%1;b="foo-%32-bar"',
                '%12%34%',
                ['a' => '%1', 'b' => 'foo-%32-bar'],
                '%12%34%;a=%1;b=foo-%32-bar',
            ],
            'RFC2231/5987-1' => [
                'attachment;filename*=UTF-8\'\'foo-%c3%a4-%e2%82%ac.html',
                'attachment',
                ['filename*' => 'UTF-8\'\'foo-%c3%a4-%e2%82%ac.html'],
                'attachment;filename*=UTF-8\'\'foo-%c3%a4-%e2%82%ac.html',
            ],
        ];
    }

    /**
     * @dataProvider valueAndParametersDataProvider
     */
    public function testParsingAndRepackOfValueAndParams(
        string $input,
        string $value,
        array $params,
        string $output = null
    ): void {
        $header = new Header(WithParamsHeaderValue::class);
        $header->add($input);
        /** @var WithParamsHeaderValue $headerValue */
        $headerValue = $header->getValues(true)[0];

        $this->assertSame($value, $headerValue->getValue());
        $this->assertSame($params, $headerValue->getParams());
        $this->assertSame($output ?? $input, $headerValue->__toString());
    }


    public function incorrectValueAndParametersDataProvider(): array
    {
        return [
            'quotedValue' => ['"value"', false, '"value"', []],
            'doubleColon' => [': value;a=b', false, ': value', ['a' => 'b']],
            'missingDelimiterBtwParams' => ['value; a=a1 b=b1', true, 'value', ['a' => 'a1'], 'value;a=a1'],
            'spacesInParamKey' => ['value;a a=b', true, 'value', [], 'value'],
            'spaces1' => ['test ; q = 1.0 ; version = 2', true, 'test', [], 'test'],
            'spaces2' => ['q = 1.0', true, 'q', [], 'q'],
            'spaces3' => ['a=b c', true, '', ['a' => 'b'], 'a=b'],
            'doubleDelimiter1' => ['value; a=a1;;b=b1', true, 'value', ['a' => 'a1'], 'value;a=a1'],
            'doubleDelimiter2' => ['value;;a=a1;b=b1', true, 'value', [], 'value'],
            'tooMoreSpaces' => ['foo bar param=1', true, 'foo bar', [], 'foo bar'],
            'invalidQuotes1' => ['a="', true, '', [], ''],
            'invalidQuotes2' => ['a="test', false, '', ['a' => 'test'], 'a=test'],
            'invalidQuotes3' => ['a=test"', true, '', [], ''],
            'invalidQuotes4' => ['a=te"st', true, '', [], ''],
            'invalidEmptyValue' => ['a=b; c=', true, '', ['a' => 'b'], 'a=b'],
            'invalidEmptyParam' => ['a=b; ;c=d', true, '', ['a' => 'b'], 'a=b'],
            'semicolonAtEnd' => ['a=b;', false, '', ['a' => 'b'], 'a=b'],
            'comma' => ['a=test,test', true, '', [], ''],
            'sameParamName' => ['a=T1;a="T2"',false, '', ['a' => 'T1'], 'a=T1'],
            'sameParamNameCase' => ['aa=T1;Aa="T2"',false, '', ['aa' => 'T1'], 'aa=T1'],
            'brokenToken' => ['a=foo[1](2).html', true, '', [], ''],
            'brokenSyntax1' => ['a==b', true, '', [], ''],
            'brokenSyntax2' => ['value; a *=b', true, 'value', [], 'value'],
            'brokenSyntax3' => ['value;a *=b', true, 'value', [], 'value'],
            # Invalid syntax but most browsers accept the umlaut with warn
            'brokenToken2' => ['a=foo-ä.html', false, '', ['a' => 'foo-ä.html']],
        ];
    }
    /**
     * @dataProvider incorrectValueAndParametersDataProvider
     */
    public function testParsingAndRepackOfIncorrectValueAndParams(
        string $input,
        bool $withError,
        string $value,
        array $params,
        string $output = null
    ): void {
        $header = new Header(WithParamsHeaderValue::class);
        $header->add($input);
        /** @var WithParamsHeaderValue $headerValue */
        $headerValue = $header->getValues(false)[0];

        $this->assertSame($withError, $headerValue->getError() !== null);
        $this->assertSame($value, $headerValue->getValue());
        $this->assertSame($params, $headerValue->getParams());
        $this->assertSame($output ?? $input, $headerValue->__toString());
    }
}
