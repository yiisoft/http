<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Accept;

/**
 * @link https://tools.ietf.org/html/rfc7231#section-5.3.5
 */
final class AcceptLanguage extends Accept
{
    public const NAME = 'Accept-Language';
    public const VALUE_SEPARATOR = '-';
}
