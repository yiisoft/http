<?php

namespace Yiisoft\Http\Header;

/**
 * @see https://tools.ietf.org/html/rfc7231#section-5.3.1
 */
interface WithQualityParam extends ListedValues, WithParams
{
    public function getQuality();
}
