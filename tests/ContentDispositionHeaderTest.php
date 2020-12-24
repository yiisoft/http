<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Http\ContentDispositionHeader;

final class ContentDispositionHeaderTest extends TestCase
{
    public function testName(): void
    {
        $this->assertSame('Content-Disposition', ContentDispositionHeader::name());
    }

    public function dataValue(): array
    {
        return [
            'inlineOnly' => [ContentDispositionHeader::INLINE, null, 'inline'],
            'inlineWithAsciiFileName' => [ContentDispositionHeader::INLINE, 'foo.html', 'inline; filename="foo.html"'],
            'attachmentOnly' => [ContentDispositionHeader::ATTACHMENT, null, 'attachment'],
            'attachmentWithAsciiFileName' => [
                ContentDispositionHeader::ATTACHMENT,
                'foo.html',
                'attachment; filename="foo.html"',
            ],
            'fileNameWithSlashes' => [
                ContentDispositionHeader::ATTACHMENT,
                'f\o/o.html',
                'attachment; filename="f_o_o.html"',
            ],
            'fileNameWithSpace' => [
                ContentDispositionHeader::INLINE,
                'hello world.html',
                'inline; filename="hello world.html"; filename*=utf-8\'\'hello%20world.html',
            ],
            'fileNameWithQuote' => [
                ContentDispositionHeader::INLINE,
                'a\'b.png',
                'inline; filename="a\'b.png"; filename*=utf-8\'\'a%27b.png',
            ],
            'fileNameWithDoubleQuote' => [
                ContentDispositionHeader::INLINE,
                'a"b.png',
                'inline; filename="a\"b.png"; filename*=utf-8\'\'a%22b.png',
            ],
            'fileNameWithProcent' => [
                ContentDispositionHeader::INLINE,
                'a%20b.png',
                'inline; filename="a_20b.png"',
            ],
            'fileNameWithUnicode' => [
                ContentDispositionHeader::INLINE,
                'aёüöäßb.png',
                'inline; filename="aeuoassb.png"; filename*=utf-8\'\'a%D1%91%C3%BC%C3%B6%C3%A4%C3%9Fb.png',
            ],
            'fileNameWithEmoji' => [
                ContentDispositionHeader::INLINE,
                'a😎b.png',
                'inline; filename="a_b.png"; filename*=utf-8\'\'a%F0%9F%98%8Eb.png',
            ],
            'fileNameWithDelete' => [
                ContentDispositionHeader::ATTACHMENT,
                "a\x7f.png",
                'attachment; filename="a_.png"; filename*=utf-8\'\'a%7F.png',
            ],
        ];
    }

    /**
     * @dataProvider dataValue
     *
     * @param string $type
     * @param string|null $fileName
     * @param string $expected
     */
    public function testValue(string $type, ?string $fileName, string $expected): void
    {
        $this->assertSame($expected, ContentDispositionHeader::value($type, $fileName));
    }

    public function testIncorrectTypeInValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Disposition type must be/');
        ContentDispositionHeader::value('unknown');
    }
}
