<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Accept;

/**
 * @see https://tools.ietf.org/html/rfc7231#section-5.3.4
 */
final class AcceptCharset extends Accept
{
    public const NAME = 'Accept-Charset';
    public const VALUE_SEPARATOR = '';
}
