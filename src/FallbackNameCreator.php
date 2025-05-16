<?php

declare(strict_types=1);

namespace Yiisoft\Http;

use function extension_loaded;

/**
 * @internal
 */
final class FallbackNameCreator
{
    /**
     * @var string[] Fallback map for transliteration used when `intl` isn't available.
     */
    private const TRANSLITERATION_MAP = [
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
        'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
        'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
        'ÿ' => 'y',
    ];

    /**
     * The rule is loose, letters will be transliterated with the characters of Basic Latin Unicode Block.
     *
     * @see https://unicode.org/reports/tr15/#Normalization_Forms_Table
     */
    private const TRANSLITERATOR = 'Any-Latin; Latin-ASCII; [\u0080-\uffff] remove';

    /**
     * @param string $fileName The file name to be transliterated. Should be a valid UTF-8 string.
     */
    public static function create(string $fileName): string
    {
        $fallbackName = self::transliterate($fileName);
        $fallbackName = str_replace("\r\n", '_', $fallbackName);
        /**
         * @var string $fallbackName We use valid regular expression, so `preg_replace()` always returns a string.
         */
        $fallbackName = preg_replace('/[^\x20-\x7e]/u', '_', $fallbackName);
        return str_replace('"', '\\"', $fallbackName);
    }

    private static function transliterate(string $fileName): string
    {
        if (!extension_loaded('intl')) {
            return strtr($fileName, self::TRANSLITERATION_MAP);
        }

        /**
         * @var string We assume that `$fileName` are valid UTF-8 strings and transliterator is valid, so
         * `transliterator_transliterate()` never returns `false`.
         */
        return transliterator_transliterate(self::TRANSLITERATOR, $fileName);
    }
}
