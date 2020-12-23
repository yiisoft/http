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
            [
                'inline',
                'face.png',
                'inline; filename="face.png"',
            ],
            [
                'inline',
                'a/b test.png',
                'inline; filename="a_b test.png"; filename*=utf-8\'\'ab%20test.png',
            ],
            [
                'attachment',
                "a\x7Fb.png",
                'attachment; filename="a_b.png"; filename*=utf-8\'\'a%7Fb.png',
            ],
            [
                'attachment',
                'a%20b.png',
                'attachment; filename="a_20b.png"; filename*=utf-8\'\'a20b.png',
            ],
        ];
    }

    /**
     * @dataProvider dataContentDispositionValue
     *
     * @param string $disposition
     * @param string $attachmentName
     * @param string $expected
     */
    public function testContentDispositionValue(string $disposition, string $attachmentName, string $expected): void
    {
        $this->assertSame($expected, HeaderHelper::contentDispositionValue($disposition, $attachmentName));
    }
}
