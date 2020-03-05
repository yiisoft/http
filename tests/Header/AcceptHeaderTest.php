<?php

namespace Yiisoft\Http\Tests\Header;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\AcceptHeader;
use Yiisoft\Http\Header\Value\Accept\Accept;
use Yiisoft\Http\Header\Value\Accept\AcceptCharset;
use Yiisoft\Http\Header\Value\Accept\AcceptEncoding;
use Yiisoft\Http\Header\Value\Accept\AcceptLanguage;
use Yiisoft\Http\Tests\Header\Value\Stub\SortedHeaderValue;

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
        new AcceptHeader(SortedHeaderValue::class);
    }
    public function testCreateFromStringValues()
    {
        $header = (new AcceptHeader('Accept'))->withValue('*/*');

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

        $header = (new AcceptHeader('Accept-Test'))->withValues($headers);

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

        $header = (new AcceptHeader('Accept'))->withValues($headers);

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

    public function testAcceptPrioritySortingWithoutParams()
    {
        $headers = ' text/*, text/plain, text/plain;format=flowed, */*';

        $header = (new AcceptHeader('Accept'))->withValue($headers);

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
    public function testAcceptPrioritySortingOfIncorrectValuesDataWithoutParams()
    {
        $headers = 'foo/bar/*, foo/bar/baz, */bar, */*/*, foo/*/*, foo/*/baz, foo/bar';

        $header = (new AcceptHeader('Accept'))->withValue($headers);

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
    public function testAcceptCreateFromManyMixedStringValues()
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

        $header = (new AcceptHeader('Accept'))->withValues($headers);

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

    public function testAcceptCharsetPrioritySortingWithoutParams()
    {
        $headers = '*, utf-8, iso-8859-5';

        $header = AcceptCharset::createHeader()->withValue($headers);

        $this->assertSame('Accept-Charset', $header->getName());
        $this->assertSame(
            [
                'utf-8',
                'iso-8859-5',
                '*',
            ],
            $header->getStrings()
        );
    }
    public function testAcceptCharsetSortingManyValues()
    {
        $headers = ['iso-8859-5, unicode-1-1;q=0.8, utf-8, undef/ned, *;q=0'];

        $header = AcceptCharset::createHeader()->withValues($headers);

        $this->assertSame('Accept-Charset', $header->getName());
        $this->assertSame(
            [
                'iso-8859-5',
                'utf-8',
                'undef/ned',
                'unicode-1-1;q=0.8',
                '*;q=0',
            ],
            $header->getStrings()
        );
    }

    public function testAcceptEncodingPrioritySortingWithoutParams()
    {
        $headers = '*, compress, some/encoding, gzip';

        $header = AcceptEncoding::createHeader()->withValue($headers);

        $this->assertSame('Accept-Encoding', $header->getName());
        $this->assertSame(
            [
                'compress',
                'some/encoding',
                'gzip',
                '*',
            ],
            $header->getStrings()
        );
    }
    public function testAcceptEncodingSortingManyValues()
    {
        $headers = [
            'compress, *',
            '',
            'gzip;q=1.0, identity; q=0.5, deflate;q=0',
        ];

        $header = AcceptEncoding::createHeader()->withValues($headers);

        $this->assertSame('Accept-Encoding', $header->getName());
        $this->assertSame(
            [
                'compress',
                '',
                'gzip',
                '*',
                'identity;q=0.5',
                'deflate;q=0',
            ],
            $header->getStrings()
        );
    }

    public function testAcceptLanguagePrioritySortingWithoutParams()
    {
        $headers = [
            'zh-Hant-CN-x',
            'zh-Hant-CN-x-private1-private2',
            'zh-Hant-CN',
            'zh-Hant-CN-x-private1',
        ];

        $header = AcceptLanguage::createHeader()->withValues($headers);

        $this->assertSame('Accept-Language', $header->getName());
        $this->assertSame(
            [
                'zh-Hant-CN-x-private1-private2',
                'zh-Hant-CN-x-private1',
                'zh-Hant-CN-x',
                'zh-Hant-CN',
            ],
            $header->getStrings()
        );
    }
    public function testAcceptLanguageCreateFromManyMixedStringValues()
    {
        $headers = [
            'da',
            'zh-Hant-CN-x-private1',
            'fr-FR',
            'fr',
            '*;q=0.1',
            'en-gb;q=0.8',
            'en;q=0.7',
            'bg;q=0.1',
            'ru-RU;q=0.1',
        ];

        $header = AcceptLanguage::createHeader()->withValues($headers);

        $this->assertSame('Accept-Language', $header->getName());
        $this->assertSame(
            [
                'zh-Hant-CN-x-private1',
                'fr-FR',
                'da',
                'fr',
                'en-gb;q=0.8',
                'en;q=0.7',
                'ru-RU;q=0.1',
                'bg;q=0.1',
                '*;q=0.1',
            ],
            $header->getStrings()
        );
    }
}
