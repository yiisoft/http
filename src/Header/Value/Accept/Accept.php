<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Accept;

use Yiisoft\Http\Header\AcceptHeader;
use Yiisoft\Http\Header\Internal\WithParamsHeaderValue;

/**
 * @link https://tools.ietf.org/html/rfc7231#section-5.3.2
 */
class Accept extends WithParamsHeaderValue
{
    public const NAME = 'Accept';
    public const VALUE_SEPARATOR = '/';

    protected const PARSING_LIST = true;
    protected const PARSING_Q_PARAM = true;

    final public static function createHeader(): AcceptHeader
    {
        return new AcceptHeader(static::class);
    }
}
