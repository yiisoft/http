<?php

declare(strict_types=1);

namespace Yiisoft\Http\Tests\Header\Value\Stub;

final class DirectivesHeaderValue extends \Yiisoft\Http\Header\Internal\DirectivesHeaderValue
{
    public const NAME = 'Test-Directives';

    public const NUMERIC = 'numeric';
    public const EMPTY = 'empty';
    public const HEADER_LIST = 'header-list';
    public const LIST_OR_EMPTY = 'list-or-empty';

    public const DIRECTIVES = [
        self::NUMERIC => self::ARG_DELTA_SECONDS,
        self::EMPTY => self::ARG_EMPTY,
        self::HEADER_LIST => self::ARG_HEADERS_LIST,
        self::LIST_OR_EMPTY => self::ARG_HEADERS_LIST | self::ARG_EMPTY,
    ];

    protected const PARSING_LIST = true;
}
