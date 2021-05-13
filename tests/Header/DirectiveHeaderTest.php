<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\Header\DirectiveHeader;
use Yiisoft\Http\Tests\Header\Value\Stub\DirectivesHeaderValue;
use Yiisoft\Http\Tests\Header\Value\Stub\DummyHeaderValue;

class DirectiveHeaderTest extends TestCase
{
    public function testHeaderValueClassPassed()
    {
        $values = new DirectiveHeader(DirectivesHeaderValue::class);
        $this->assertSame('Test-Directives', $values->getName());
        $this->assertSame(DirectivesHeaderValue::class, $values->getValueClass());
    }

    public function testErrorWithHeaderClass()
    {
        $this->expectException(InvalidArgumentException::class);
        new DirectiveHeader(DummyHeaderValue::class);
    }

    public function testCreateFromStringValue()
    {
        $header = (new DirectiveHeader('Directive-Test'))->withValue('value');

        $this->assertSame('Directive-Test', $header->getName());
        $this->assertSame(['value'], $header->getStrings());
    }

    public function testCreateFromStringValueWithArgument()
    {
        $header = (new DirectiveHeader('Directive-Test'))->withValue('value=tEst');

        $this->assertSame('Directive-Test', $header->getName());
        $this->assertSame(['value=tEst'], $header->getStrings());
    }

    public function testCreateDirectivesFromFewStringValues()
    {
        $headers = [
            'max-age=123',
            'no-store',
            'proxy-revalidate, private="Connect, Host"',
        ];
        $header = (new DirectiveHeader('Cache-Control'))->withValues($headers);
        $this->assertSame('Cache-Control', $header->getName());
        $this->assertSame(
            [
                'max-age=123',
                'no-store',
                'proxy-revalidate',
                'private="Connect, Host"',
            ],
            $header->getStrings()
        );
    }
}
