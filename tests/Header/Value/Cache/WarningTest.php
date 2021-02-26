<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header\Value\Cache;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Value\Cache\Warning;

class WarningTest extends TestCase
{
    public function testWithDatasetImmutability()
    {
        $origin = new Warning();

        $clone = $origin->withDataset(100, 'localhost', 'test', new DateTimeImmutable());

        // the default values of the original object have not changed
        $this->assertNull($origin->getCode());
        $this->assertNull($origin->getAgent());
        $this->assertNull($origin->getText());
        $this->assertNull($origin->getDate());
        // changes applied
        $this->assertSame(100, $clone->getCode());
        $this->assertSame('localhost', $clone->getAgent());
        $this->assertSame('test', $clone->getText());
        $this->assertNotNull($clone->getDate());
        // immutability
        $this->assertSame(get_class($origin), get_class($clone));
        $this->assertNotSame($origin, $clone);
    }

    public function testToStringWithoutDate()
    {
        $headerValue = (new Warning())->withDataset(100, '-', 'test');

        $this->assertSame('100 - "test"', (string)$headerValue);
    }

    public function testWithValueParsing()
    {
        $headerValue = (new Warning())->withValue('100 localhost "test" "Wed, 01 Jan 2020 00:00:00 GMT"');

        $this->assertSame(100, $headerValue->getCode());
        $this->assertSame('localhost', $headerValue->getAgent());
        $this->assertSame('test', $headerValue->getText());
        $this->assertNotNull($headerValue->getDate());
        $this->assertSame('100 localhost "test" "Wed, 01 Jan 2020 00:00:00 GMT"', (string)$headerValue);
        $this->assertFalse($headerValue->hasError());
    }

    public function withValueDataProvider(): array
    {
        return [
            'spaces' => ['  100    -   ""    ', 100, '-', ''],
            'spaces+time' => ['  100  - ""   "Wed, 01 Jan 2020 00:00:00 GMT"  ', 100, '-', '', '2020-01-01-00-00-00'],
            'agent-url' => ['100 www.example.com ""', 100, 'www.example.com', ''],
            'agent-url:port' => ['100 www.example.com:80 ""', 100, 'www.example.com:80', ''],
            'text-empty' => ['100 - ""', 100, '-', ''],
            'text-spaced' => ['100 - " test text "', 100, '-', ' test text '],
            'text-quote' => ['100 - "\\""', 100, '-', '"'],
            'text-bs' => ['100 - "\\\\"', 100, '-', '\\'],
            'time-RFC-7231' => ['100 - "" "Fri, 04 Jul 2008 08:42:36 GMT"', 100, '-', '', '2008-07-04-08-42-36'],
            'time-RFC-850' => ['100 - "" "Friday, 04-Jul-08 08:42:36 GMT"', 100, '-', '', '2008-07-04-08-42-36'],
            'time-deprecated' => ['100 - "" "Fri Jul 4 08:42:36 2008"', 100, '-', '', '2008-07-04-08-42-36'],
        ];
    }

    /**
     * @dataProvider withValueDataProvider
     */
    public function testwithValueCorrect(
        string $input,
        int $code,
        string $agent,
        string $text,
        string $date = null
    ) {
        $headerValue = (new Warning())->withValue($input);
        $this->assertFalse($headerValue->hasError());
        $this->assertSame($code, $headerValue->getCode());
        $this->assertSame($agent, $headerValue->getAgent());
        $this->assertSame($text, $headerValue->getText());
        $this->assertSame(
            $date,
            $headerValue->getDate() === null
            ? null
            : $headerValue->getDate()->format('Y-m-d-H-i-s')
        );
    }

    public function withValueIncorrectDataProvider(): array
    {
        return [
            'no-code' => [' - ""'],
            'no-agent' => ['100  ""'],
            'no-text' => ['100 -'],
            'code-first-zero' => ['012 - ""'],
            'code-zero' => ['000 - ""'],
            'code-hex-1' => ['0xff - ""'],
            'code-hex-2' => ['12A - ""'],
            'code-quoted' => ['"100" - ""'],
            'text-unescaped-quote' => ['100 - """'],
            'text-end-bs' => ['100 - "\\"'],
            'datetime-1' => ['100 - "" "phrase"'],
            'datetime-2' => ['100 - "" "now"'],
            'datetime-3' => ['100 - "" now'],
            'datetime-RFC-7231-1' => ['100 - "" "Fri, 99 Jul 2008 08:42:36 GMT"'],
            'datetime-RFC-7231-2' => ['100 - "" "Fri, 01 Jul 2008 30:42:36 GMT"'],
            'datetime-RFC-850-1' => ['100 - "" "Friday, 99-Jul-08 08:42:36 GMT"'],
            'datetime-RFC-850-2' => ['100 - "" "Friday, 01-Jul-08 30:42:36 GMT"'],
            'datetime-deprecated-1' => ['100 - "" "Fri Jul 99 08:42:36 2008"'],
        ];
    }

    /**
     * @dataProvider withValueIncorrectDataProvider
     */
    public function testWithValueIncorrectCases(
        string $input
    ) {
        $headerValue = (new Warning())->withValue($input);
        $this->assertTrue($headerValue->hasError());
    }
}
