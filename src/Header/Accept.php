<?php

namespace Yiisoft\Http\Header;

/**
 * @see https://tools.ietf.org/html/rfc7231#section-5.3.2
 */
final class Accept extends HeaderValue implements WithQualityParam
{
    public const NAME = 'Accept';
}
