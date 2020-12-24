<?php

declare(strict_types=1);

namespace Yiisoft\Http;

use Yiisoft\Strings\Inflector;

final class HeaderHelper
{
    /**
     * Returns Content-Disposition header value that is safe to use with both old and new browsers.
     *
     * Fallback name:
     *
     * - Causes issues if contains non-ASCII characters with codes less than 32 or more than 126.
     * - Causes issues if contains urlencoded characters (starting with `%`) or `%` character. Some browsers interpret
     *   `filename="X"` as urlencoded name, some don't.
     * - Causes issues if contains path separator characters such as `\` or `/`.
     * - Since value is wrapped with `"`, it should be escaped as `\"`.
     * - Since input could contain non-ASCII characters, fallback is obtained by transliteration.
     *
     * UTF name:
     *
     * - Causes issues if contains path separator characters such as `\` or `/`.
     * - Should be urlencoded since headers are ASCII-only.
     * - Could be omitted if it exactly matches fallback name.
     *
     * @see https://tools.ietf.org/html/rfc6266#page-5
     *
     * @param string $disposition
     * @param string|null $fileName
     *
     * @return string
     */
    public static function contentDispositionValue(string $disposition, ?string $fileName = null): string
    {
        $header = $disposition;

        if ($fileName === null) {
            return $header;
        }

        $fallbackName = str_replace(
            ['%', '/', '\\', '"', "\x7F"],
            ['_', '_', '_', '\\"', '_'],
            (new Inflector())->toTransliterated($fileName, Inflector::TRANSLITERATE_LOOSE)
        );
        $utfName = rawurlencode(str_replace(['%', '/', '\\'], '', $fileName));

        $header .= "; filename=\"{$fallbackName}\"";
        if ($utfName !== $fallbackName) {
            $header .= "; filename*=utf-8''{$utfName}";
        }

        return $header;
    }
}
