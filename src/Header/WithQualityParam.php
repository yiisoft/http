<?php

namespace Yiisoft\Http\Header;

/**
 * @see https://tools.ietf.org/html/rfc7231#section-5.3.1
 */
interface WithQualityParam extends ListedValues, WithParams
{
    /**
     * @return string value between 0.000 and 1.000
     */
    public function getQuality(): string;
}
