<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Cache;

use Yiisoft\Http\Header\Internal\DirectivesHeaderValue;

/**
 * @link https://tools.ietf.org/html/rfc7234#section-5.2
 */
class CacheControl extends DirectivesHeaderValue
{
    public const NAME = 'Cache-Control';

    // Request Directives
    public const MAX_AGE = 'max-age';
    public const MAX_STALE = 'max-stale';
    public const MIN_FRESH = 'min-fresh';
    public const NO_CACHE = 'no-cache';
    public const NO_STORE = 'no-store';
    public const NO_TRANSFORM = 'no-transform';
    public const ONLY_IF_CACHED = 'only-if-cached';
    public const MUST_REVALIDATE = 'must-revalidate';
    public const PUBLIC = 'public';
    public const PRIVATE = 'private';
    public const PROXY_REVALIDATE = 'proxy-revalidate';
    public const S_MAXAGE = 's-maxage';

    /**
     * Request and Response Directives:
     * @link https://tools.ietf.org/html/rfc7234#section-5.2.1
     * @link https://tools.ietf.org/html/rfc7234#section-5.2.2
     */
    public const DIRECTIVES = [
        // Request Directives
        self::MAX_STALE => self::ARG_DELTA_SECONDS,
        self::MIN_FRESH => self::ARG_DELTA_SECONDS,
        self::ONLY_IF_CACHED => self::ARG_EMPTY,
        // Response Directives
        self::MUST_REVALIDATE => self::ARG_EMPTY,
        self::PUBLIC => self::ARG_EMPTY,
        self::PRIVATE => self::ARG_HEADERS_LIST | self::ARG_EMPTY,
        self::PROXY_REVALIDATE => self::ARG_EMPTY,
        self::S_MAXAGE => self::ARG_DELTA_SECONDS,
        // Both
        self::NO_CACHE => self::ARG_HEADERS_LIST | self::ARG_EMPTY,
        self::NO_STORE => self::ARG_EMPTY,
        self::NO_TRANSFORM => self::ARG_EMPTY,
        self::MAX_AGE => self::ARG_DELTA_SECONDS,
    ];
}
