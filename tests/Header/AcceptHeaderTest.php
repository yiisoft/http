<?php

namespace Yiisoft\Http\Tests\Header;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\AcceptHeader;
use Yiisoft\Http\Header\Value\Accept\Accept;
use Yiisoft\Http\Tests\Header\Value\Stub\QualityHeaderValue;

class AcceptHeaderTest extends TestCase
{
    public function testHeaderValueClassPassed()
    {
        $values = new AcceptHeader(Accept::class);
        $this->assertSame('Accept', $values->getName());
        $this->assertSame(Accept::class, $values->getValueClass());
    }
    public function testErrorWithHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new AcceptHeader(QualityHeaderValue::class);
    }
    public function testCreateFromStringValues()
    {
        $header = (new AcceptHeader('Accept'))->add('*/*');

        $this->assertSame('Accept', $header->getName());
        $this->assertSame(['*/*'], $header->getStrings());
    }
    public function testCreateFromFewStringValues()
    {
        $headers = [
            'text/*;q=0.3',
            'text/html',
            '*/*;q=0.001',
            'text/plain;q=0.5',
        ];

        $header = (new AcceptHeader('Accept-Test'))->addArray($headers);

        $this->assertSame('Accept-Test', $header->getName());
        $this->assertSame(
            [
                'text/html',
                'text/plain;q=0.5',
                'text/*;q=0.3',
                '*/*;q=0.001',
            ],
            $header->getStrings()
        );
    }
    public function testParamsPrioritySortingWithSameQuality()
    {
        $headers = [
            'text/html',
            'text/plain;foo=1;bar=2',
            'text/plain;foo=3',
            'text/plain;foo=1',
            'text/plain',
            'text/plain;foo=2',
        ];

        $header = (new AcceptHeader('Accept'))->addArray($headers);

        $this->assertSame('Accept', $header->getName());
        $this->assertSame(
            [
                'text/plain;foo=1;bar=2',
                'text/plain;foo=3',
                'text/plain;foo=1',
                'text/plain;foo=2',
                'text/html',
                'text/plain',
            ],
            $header->getStrings()
        );
    }
    public function testPrioritySortingWithoutParams()
    {
        $headers = ' text/*, text/plain, text/plain;format=flowed, */*';

        $header = (new AcceptHeader('Accept'))->add($headers);

        $this->assertSame('Accept', $header->getName());
        $this->assertSame(
            [
                'text/plain;format=flowed',
                'text/plain',
                'text/*',
                '*/*',
            ],
            $header->getStrings()
        );
    }
    public function testPrioritySortingOfIncorrectValuesDataWithoutParams()
    {
        $headers = 'foo/bar/*, foo/bar/baz, */bar, */*/*, foo/*/*, foo/*/baz, foo/bar';

        $header = (new AcceptHeader('Accept'))->add($headers);

        $this->assertSame('Accept', $header->getName());
        $this->assertSame(
            [
                'foo/bar/baz',
                'foo/*/baz',
                'foo/bar/*',
                'foo/*/*',
                '*/*/*',
                'foo/bar',
                '*/bar',
            ],
            $header->getStrings()
        );
    }
    public function testCreateFromManyMixedStringValues()
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

        $header = (new AcceptHeader('Accept'))->addArray($headers);

        $this->assertSame('Accept', $header->getName());
        $this->assertSame(
            [
                'text/html;level=1',
                'text/plain;format=flowed',
                'text/plain',
                'text/*',
                '*/*',
                'text/html;q=0.7',
                '*/*;q=0.5',
                'text/html;level=2;q=0.4',
                'text/*;q=0.3',
            ],
            $header->getStrings()
        );
    }
}
