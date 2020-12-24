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
            'base' => [
                'inline',
                'face.png',
                'inline; filename="face.png"',
            ],
            'withoutFileName' => [
                'attachment',
                null,
                'attachment',
            ],
            'slash' => [
                'inline',
                'a/\b.png',
                'inline; filename="a__b.png"; filename*=utf-8\'\'ab.png',
            ],
            'space' => [
                'inline',
                'a b.png',
                'inline; filename="a b.png"; filename*=utf-8\'\'a%20b.png',
            ],
            'hex' => [
                'attachment',
                "a\x41.png",
                'attachment; filename="aA.png"',
            ],
            'x7f' => [
                'attachment',
                "a\x7f.png",
                'attachment; filename="a_.png"; filename*=utf-8\'\'a%7F.png',
            ],
            'procent' => [
                'attachment',
                'a%20b.png',
                'attachment; filename="a_20b.png"; filename*=utf-8\'\'a20b.png',
            ],
            'unicode' => [
                'inline',
                'aёüöäßb.png',
                'inline; filename="aeuoassb.png"; filename*=utf-8\'\'a%D1%91%C3%BC%C3%B6%C3%A4%C3%9Fb.png',
            ],
            'quotes' => [
                'inline',
                'a"b"c\'d\'.png',
                'inline; filename="a\"b\"c\'d\'.png"; filename*=utf-8\'\'a%22b%22c%27d%27.png',
            ],
            'emoji' => [
                'inline',
                'a😎b.png',
                'inline; filename="a😎b.png"; filename*=utf-8\'\'a%F0%9F%98%8Eb.png',
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
