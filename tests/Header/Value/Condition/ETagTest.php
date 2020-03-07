<?php

namespace Yiisoft\Http\Tests\Header\Value\Condition;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\Value\Condition\ETag;

class ETagTest extends TestCase
{
    public function testWithTagImmutability()
    {
        $origin = new ETag();

        $clone = $origin->withTag('test', false);

        // the default values of the original object have not changed
        $this->assertSame('', $origin->getTag());
        $this->assertTrue($origin->isWeak());
        // changes applied
        $this->assertSame('test', $clone->getTag());
        $this->assertFalse($clone->isWeak());
        // immutability
        $this->assertSame(get_class($origin), get_class($clone));
        $this->assertNotSame($origin, $clone);
    }
    public function testToStringFromTag()
    {
        $origin = (new ETag())->withValue('"origin-tag"');

        $clone = $origin->withTag('new-tag', true);

        $this->assertSame('W/"new-tag"', (string)$clone);
    }
    public function testToStringFromValue()
    {
        $origin = (new ETag())->withTag('origin-tag', false);

        $clone = $origin->withValue('W/"new-tag"');

        $this->assertSame('W/"new-tag"', (string)$clone);
    }
    public function testToStringFromIncorrectValue()
    {
        $origin = (new ETag())->withTag('origin-tag', false);

        $clone = $origin->withValue('some-incorrect-value');

        $this->assertSame('some-incorrect-value', (string)$clone);
    }

    public function withValueDataProvider(): array
    {
        return [
            'weak1' => ['W/"value"', false, true, 'value'],
            'weak2' => ['"value"', false, false, 'value'],
            'backslashes' => ['"\\this-is-not\\\\-a-quoted-pair\\"', false, false, '\\this-is-not\\\\-a-quoted-pair\\'],
            'wo-quotes' => ['value', true, true, ''],
            'spaced' => ['"spaced string"', true, true, ''],
            'with-quotes' => ['""quoted""', true, true, ''],
            'with-escaped-quotes' => ['"\\"quoted\\""', true, true, ''],
            'hard-space' => ['"' . chr(127) . '"', true, true, ''],
        ];
    }
    /**
     * @dataProvider withValueDataProvider
     */
    public function testValueParsing(string $input, bool $hasError, bool $isWeak, string $tag)
    {
        $headerValue = (new ETag())->withValue($input);

        $this->assertSame($tag, $headerValue->getTag());
        $this->assertSame($isWeak, $headerValue->isWeak());
        $this->assertSame($hasError, $headerValue->hasError());
    }
}
