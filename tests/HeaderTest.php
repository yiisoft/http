<?php

namespace Yiisoft\Http\Tests\Header;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Accept;
use Yiisoft\Http\Header\Date;
use Yiisoft\Http\Header\HeaderValue;
use Yiisoft\Http\Header;
use Yiisoft\Http\Tests\Header\Stub\QualityHeaderValue;

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
                'a=tes\'t;sp= s p ;test=\'test\';test2="\\"quoted\\" test"',
            ],
            'slashes' => [
                'a="\\t\\e\\s\\t";b="te\\\\st";c="\\"\\"',
                '',
                ['a' => 'test', 'b' => 'te\\st', 'c' => '""'],
                'a=test;b="te\\\\st";c="\\"\\""',
            ],
            'withSpacesAfterDelimiters' => [
                'test; q=1.0; version=2',
                'test',
                ['q' => '1.0', 'version' => '2'],
                'test;q=1.0;version=2',
            ],
            'spacesAroundDelimiters' => [
                'test ; q = 1.0 ; version = 2',
                'test',
                ['q' => '1.0', 'version' => '2'],
                'test;q=1.0;version=2',
            ],
            'emptyValueWithParam' => ['param=value', '', ['param' => 'value']],
            'emptyValueWithParam2' => ['param=value;a=b', '', ['param' => 'value', 'a' => 'b']],
            'missingDelimiterBtwValueAndParam' => ['value   a=a1', 'value', ['a' => 'a1'], 'value;a=a1'],
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
        $header = new Header(QualityHeaderValue::class);
        $header->add($input);
        /** @var QualityHeaderValue $headerValue */
        $headerValue = $header->getValues()[0];

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
            'doubleDelimiter1' => ['value; a=a1;;b=b1', true, 'value', ['a' => 'a1'], 'value;a=a1'],
            'doubleDelimiter2' => ['value;;a=a1;b=b1', true, 'value', [], 'value'],
            'tooMoreSpaces' => ['foo bar param=1', true, 'foo', [], 'foo'],
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
        $header = new Header(QualityHeaderValue::class);
        $header->add($input);
        /** @var QualityHeaderValue $headerValue */
        $headerValue = $header->getValues()[0];

        $this->assertSame($withError, $headerValue->getError() !== null);
        $this->assertSame($value, $headerValue->getValue());
        $this->assertSame($params, $headerValue->getParams());
        $this->assertSame($output ?? $input, $headerValue->__toString());
    }
}
