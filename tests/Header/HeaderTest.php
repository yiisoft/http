<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\Value\Accept\Accept;
use Yiisoft\Http\Header\Internal\BaseHeaderValue;
use Yiisoft\Http\Header\Value\Date;
use Yiisoft\Http\Header\Value\Unnamed\SimpleValue;
use Yiisoft\Http\Tests\Header\Value\Stub\ListedValuesHeaderValue;
use Yiisoft\Http\Tests\Header\Value\Stub\ListedValuesWithParamsHeaderValue;
use Yiisoft\Http\Tests\Header\Value\Stub\SortedHeaderValue;
use Yiisoft\Http\Tests\Header\Value\Stub\WithParamsHeaderValue;

class HeaderTest extends TestCase
{
    public function testHeaderValueClassPassed()
    {
        $values = new Header(Date::class);
        $this->assertSame('Date', $values->getName());
        $this->assertSame(Date::class, $values->getValueClass());
    }

    public function testErrorWhenHeaderValueHasNoHeaderName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/no header name/');
        new Header(SimpleValue::class);
    }

    public function testErrorWithHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/not a header/');
        new Header(BaseHeaderValue::class);
    }

    public function testErrorIfNotHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new Header(\DateTimeImmutable::class);
    }

    public function testWithValueImmutability()
    {
        $header = new Header('WWW-Authenticate');

        $clone = $header->withValue('test');

        $this->assertSame(get_class($header), get_class($clone));
        $this->assertNotSame($header, $clone);
    }

    public function testWithValuesImmutability()
    {
        $header = new Header('WWW-Authenticate');

        $clone = $header->withValues(['test']);

        $this->assertSame(get_class($header), get_class($clone));
        $this->assertNotSame($header, $clone);
    }

    public function testCreateFromOneStringValue()
    {
        $headers = ['Newauth realm="apps", type=1, title="Login to \\"apps\\"", Basic realm="simple"'];

        $header = (new Header('WWW-Authenticate'))->withValues($headers);

        $this->assertSame('WWW-Authenticate', $header->getName());
        $this->assertSame($headers, $header->getStrings());
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

        $header = (new Header('accept'))->withValues($headers);

        $this->assertSame('accept', $header->getName());
        $this->assertSame($headers, $header->getStrings());
    }

    public function testAddObject()
    {
        $headers = [
            'text/*;q=0.3',
            'text/html;q=0.7',
        ];
        $header = (new Header(Accept::class))->withValues($headers);

        $header = $header->withValue(new Accept('*/*'));

        $this->assertSame(Accept::NAME, $header->getName());
        $this->assertSame(['text/*;q=0.3', 'text/html;q=0.7', '*/*'], $header->getStrings());
    }

    public function testExceptionWhenAddOtherClassObject()
    {
        $headers = [
            'text/*;q=0.3',
            'text/html;q=0.7',
        ];
        $header = (new Header(Accept::class))->withValues($headers);

        $this->expectException(InvalidArgumentException::class);

        $header->withValue(new Date('*/*'));
    }

    public function valueAndParametersDataProvider(): array
    {
        return [
            'empty' => ['', '', []],
            'noParams' => ['test', 'test', []],
            'withParams' => ['test;q=1;version=2', 'test', ['q' => '1', 'version' => '2']],
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
        $header = (new Header(WithParamsHeaderValue::class))->withValue($input);
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
            'comma1' => ['foo, bar;a=test', false, 'foo, bar', ['a' => 'test'], 'foo, bar;a=test'],
            'comma2' => ['a=test,test', true, '', [], ''],
            'sameParamName' => ['a=T1;a="T2"',false, '', ['a' => 'T1'], 'a=T1'],
            'sameParamNameCase' => ['aa=T1;Aa="T2"',false, '', ['aa' => 'T1'], 'aa=T1'],
            'brokenToken' => ['a=foo[1](2).html', true, '', [], ''],
            'brokenSyntax1' => ['a==b', true, '', [], ''],
            'brokenSyntax2' => ['value; a *=b', true, 'value', [], 'value'],
            'brokenSyntax3' => ['value;a *=b', true, 'value', [], 'value'],
            // Invalid syntax but most browsers accept the umlaut with warn
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
        $header = (new Header(WithParamsHeaderValue::class))->withValue($input);
        /** @var WithParamsHeaderValue $headerValue */
        $headerValue = $header->getValues(false)[0];

        $this->assertSame($withError, $headerValue->hasError());
        $this->assertSame($value, $headerValue->getValue());
        $this->assertSame($params, $headerValue->getParams());
        $this->assertSame($output ?? $input, $headerValue->__toString());
    }

    public function qualityParametersDataProvider(): array
    {
        return [
            'q1' => ['1', true, '1'],
            'q1.0' => ['1.0', true, '1'],
            'q1.000' => ['1.000', true, '1'],
            'q1.0000' => ['1.0000', false],
            'q1.' => ['1.', false],
            'q1.1' => ['1.1', false],
            'q2.0' => ['2.0', false],
            'q-0.001' => ['-0.001', false],
            'q-0' => ['-0', false],
            'q<0' => ['-1', false],
            'q0' => ['0', true, '0'],
            'q0.0' => ['0.0', true, '0'],
            'q0.000' => ['0.000', true, '0'],
            'q0.0000' => ['0.0000', false],
            'q0.0001' => ['0.0001', false],
            'q0.05' => ['0.05', true, '0.05'],
            'q0,05' => ['0,05', false],
        ];
    }

    /**
     * @dataProvider qualityParametersDataProvider
     */
    public function testParsingAndRepackOfQualityParams(
        string $setQuality,
        bool $result,
        ?string $getQuality = null
    ): void {
        $defaultValue = '0.987';
        $headerValue = (new SortedHeaderValue())->withParams(['q' => $defaultValue]);

        $this->assertSame($result, $headerValue->setQuality($setQuality));
        $this->assertSame($getQuality ?? $defaultValue, $headerValue->getQuality());
    }

    public function listedValuesDataProvider(): array
    {
        return [
            'twoSimple' => ['value1,value2', ['value1', 'value2']],
            'moreSimple' => ['value1,value2,value1,value2', ['value1', 'value2', 'value1', 'value2']],
            'spaces' => ['  value1  ,  value2  ', ['value1', 'value2']],
            'paramsImitation' => ['value1;q=1, value2 ; q=2', ['value1;q=1', 'value2 ; q=2']],
            'commas1' => ['value1,,value2', ['value1', '', 'value2']],
            'commas2' => [',, ,', ['', '', '', '']],
            'chars' => [',!@# $%^&*()!"№;%:=-?.,', ['', '!@# $%^&*()!"№;%:=-?.', '']],
        ];
    }

    /**
     * @dataProvider listedValuesDataProvider
     */
    public function testParsingAndRepackListedValues(string $input, array $values): void
    {
        $header = (new Header(ListedValuesHeaderValue::class))->withValue($input);
        $strings = $header->getStrings(true);
        $this->assertSame($values, $strings);
    }

    public function listedValuesWithParamsDataProvider(): array
    {
        return [
            'simpleQ' => ['value1;q=1,value2;q=2', [
                ['value1', ['q' => '1']],
                ['value2', ['q' => '2']],
            ]],
            'Accept' => ['text/*;q=0.3, text/html;q=0.7, text/html;level=1, text/html;level=2;q=0.4', [
                ['text/*', ['q' => '0.3']],
                ['text/html', ['q' => '0.7']],
                ['text/html', ['level' => '1']],
                ['text/html', ['level' => '2','q' => '0.4']],
            ]],
            'spacesAccept' => [' text/*  ;  q=0.3,text/html; q=0.7  ,  text/html ;level=1,text/html ; level=2;q=0.4', [
                ['text/*', ['q' => '0.3']],
                ['text/html', ['q' => '0.7']],
                ['text/html', ['level' => '1']],
                ['text/html', ['level' => '2','q' => '0.4']],
            ]],
            'Forwarded' => [
                'for=192.0.2.43, for="[2001:db8:cafe::17]", for=unknown, for=192.0.2.60;proto=http;by=203.0.113.43',
                [
                    ['', ['for' => '192.0.2.43']],
                    ['', ['for' => '[2001:db8:cafe::17]']],
                    ['', ['for' => 'unknown']],
                    ['', ['for' => '192.0.2.60', 'proto' => 'http', 'by' => '203.0.113.43']],
                ],
            ],
            'WWW-Authenticate' => [
                'Newauth realm="apps", type=1, title="Login to \\"apps\\"", Basic realm="simple"',
                [
                    ['Newauth', ['realm' => 'apps']],
                    ['', ['type' => '1']],
                    ['', ['title' => 'Login to "apps"']],
                    ['Basic', ['realm' => 'simple']],
                ],
            ],
            'badSyntax1' => [';', [['', []]]], // added empty value
            'badSyntax2' => [';,', []], // no values added
        ];
    }

    /**
     * @dataProvider listedValuesWithParamsDataProvider
     */
    public function testParsingAndRepackListedValuesWithParams(string $input, array $valueParams): void
    {
        $header = (new Header(ListedValuesWithParamsHeaderValue::class))->withValue($input);
        $result = [];
        foreach ($header->getValues(true) as $value) {
            $result[] = [$value->getValue(), $value->getParams()];
        }
        $this->assertSame($valueParams, $result);
    }
}
