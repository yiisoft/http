<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Http\HeaderHelper;

final class HeaderHelperTest extends TestCase
{
    public function dataContentDispositionValue(): array
    {
        return [
            'inlineOnly' => ['inline', null, 'inline'],
            'inlineWithAsciiFileName' => ['inline', 'foo.html', 'inline; filename="foo.html"'],
            'attachmentOnly' => ['attachment', null, 'attachment'],
            'attachmentWithAsciiFileName' => ['attachment', 'foo.html', 'attachment; filename="foo.html"'],
            'fileNameWithSlashes' => [
                'attachment',
                'f\o/o.html',
                'attachment; filename="f_o_o.html"',
            ],
            'fileNameWithSpace' => [
                'inline',
                'hello world.html',
                'inline; filename="hello world.html"; filename*=utf-8\'\'hello%20world.html',
            ],
            'fileNameWithQuote' => [
                'inline',
                'a\'b.png',
                'inline; filename="a\'b.png"; filename*=utf-8\'\'a%27b.png',
            ],
            'fileNameWithDoubleQuote' => [
                'inline',
                'a"b.png',
                'inline; filename="a\"b.png"; filename*=utf-8\'\'a%22b.png',
            ],
            'fileNameWithProcent' => [
                'inline',
                'a%20b.png',
                'inline; filename="a_20b.png"',
            ],
            'fileNameWithUnicode' => [
                'inline',
                'aёüöäßb.png',
                'inline; filename="aeuoassb.png"; filename*=utf-8\'\'a%D1%91%C3%BC%C3%B6%C3%A4%C3%9Fb.png',
            ],
            'fileNameWithEmoji' => [
                'inline',
                'a😎b.png',
                'inline; filename="a_b.png"; filename*=utf-8\'\'a%F0%9F%98%8Eb.png',
            ],
            'fileNameWithDelete' => [
                'attachment',
                "a\x7f.png",
                'attachment; filename="a_.png"; filename*=utf-8\'\'a%7F.png',
            ],
        ];
    }

    /**
     * @dataProvider dataContentDispositionValue
     *
     * @param string $disposition
     * @param string|null $fileName
     * @param string $expected
     */
    public function testContentDispositionValue(string $disposition, ?string $fileName, string $expected): void
    {
        $this->assertSame($expected, HeaderHelper::contentDispositionValue($disposition, $fileName));
    }
}
