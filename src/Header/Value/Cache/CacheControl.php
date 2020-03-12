<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Cache;

use InvalidArgumentException;
use Yiisoft\Http\Header\CacheControlHeader;
use Yiisoft\Http\Header\ListedValues;
use Yiisoft\Http\Header\Value\BaseHeaderValue;
use Yiisoft\Http\Header\WithParams;

/**
 * @see https://tools.ietf.org/html/rfc7234#section-5.2
 */
class CacheControl extends BaseHeaderValue implements ListedValues, WithParams
{
    public const NAME = 'Cache-Control';

    // Request Directives
    public const D_MAX_AGE = 'max-age';
    public const D_MAX_STALE = 'max-stale';
    public const D_MIN_FRESH = 'min-fresh';
    public const D_NO_CACHE = 'no-cache';
    public const D_NO_STORE = 'no-store';
    public const D_NO_TRANSFORM = 'no-transform';
    public const D_ONLY_IF_CACHED = 'only-if-cached';
    public const D_MUST_REVALIDATE = 'must-revalidate';
    public const D_PUBLIC = 'public';
    public const D_PRIVATE = 'private';
    public const D_PROXY_REVALIDATE = 'proxy-revalidate';
    public const D_S_MAXAGE = 's-maxage';

    /**
     * Request Directives
     * @see https://tools.ietf.org/html/rfc7234#section-5.2.1
     */
    public const REQUEST_DIRECTIVES = [
        self::D_MAX_AGE => self::ARG_DELTA_SECONDS,
        self::D_MAX_STALE => self::ARG_DELTA_SECONDS,
        self::D_MIN_FRESH => self::ARG_DELTA_SECONDS,
        self::D_NO_CACHE => self::ARG_EMPTY,
        self::D_NO_STORE => self::ARG_EMPTY,
        self::D_NO_TRANSFORM => self::ARG_EMPTY,
        self::D_ONLY_IF_CACHED => self::ARG_EMPTY,
    ];
    /**
     * Response Directives
     * @see https://tools.ietf.org/html/rfc7234#section-5.2.2
     */
    public const RESPONSE_DIRECTIVES  = [
        self::D_MUST_REVALIDATE => self::ARG_EMPTY,
        self::D_NO_CACHE => self::ARG_HEADERS_LIST | self::ARG_EMPTY,
        self::D_NO_STORE => self::ARG_EMPTY,
        self::D_NO_TRANSFORM => self::ARG_EMPTY,
        self::D_PUBLIC => self::ARG_EMPTY,
        self::D_PRIVATE => self::ARG_HEADERS_LIST | self::ARG_EMPTY,
        self::D_PROXY_REVALIDATE => self::ARG_EMPTY,
        self::D_MAX_AGE => self::ARG_DELTA_SECONDS,
        self::D_S_MAXAGE => self::ARG_DELTA_SECONDS,
    ];

    protected const ARG_EMPTY = 1;
    protected const ARG_DELTA_SECONDS = 2;
    protected const ARG_HEADERS_LIST = 4;
    protected const ARG_CUSTOM = 8;

    protected ?string $directive = null;
    protected ?string $argument = null;
    protected int $argumentType = self::ARG_EMPTY;

    final public function __toString(): string
    {
        if ($this->directive === null) {
            return '';
        }
        if ($this->argument === null) {
            return $this->directive;
        }
        if ($this->argumentType === self::ARG_HEADERS_LIST) {
            return "{$this->directive}=\"{$this->argument}\"";
        }
        if ($this->argumentType === self::ARG_CUSTOM) {
            $argument = preg_replace('/([\\\\"])/', '\\\\$1', $this->argument);
            if (
                $argument === ''
                || strlen($argument) !== strlen($this->argument)
                || preg_match('/[^a-z0-9_]/i', $argument) === 1
            ) {
                $argument = '"' . $argument . '"';
            }
            return "{$this->directive}={$argument}";
        }
        return "{$this->directive}={$this->argument}";
    }

    final public static function createHeader(): CacheControlHeader
    {
        return new CacheControlHeader(static::class);
    }

    /**
     * @return string|null Returns null if the directive is not defined or cannot be parsed without error
     */
    final public function getDirective(): ?string
    {
        return $this->directive;
    }

    final public function hasArgument(): bool
    {
        return $this->argument !== null;
    }

    final public function getArgument(): ?string
    {
        return $this->argument;
    }

    final public function getArgumentList(): array
    {
        return $this->argument === null ? [] : explode(',', $this->argument);
    }

    /**
     * @param string $directive
     * @param string|null $argument
     * @return $this
     * @throws InvalidArgumentException
     */
    final public function withDirective(string $directive, string $argument = null): self
    {
        $clone = clone $this;
        $clone->setDirective($directive, $argument, true);
        return $clone;
    }

    final protected function setValue(string $value): void
    {
        if ($value !== '') {
            $this->setDirective($value);
        }
        parent::setValue($value);
    }

    final protected function setParams(array $params): void
    {
        $key = array_key_first($params);
        if ($key !== null) {
            $this->setDirective($key, $params[$key]);
        }
        parent::setParams($params);
    }

    final private function setDirective(string $value, string $argument = null, bool $trowError = false): bool
    {
        $name = strtolower($value);

        $fromRequestType = static::REQUEST_DIRECTIVES[$name] ?? null;
        $fromResponseType = static::RESPONSE_DIRECTIVES[$name] ?? null;

        $directiveExists = $fromRequestType !== null || $fromResponseType;
        $argumentType = $directiveExists ? $fromRequestType | $fromResponseType : self::ARG_CUSTOM;

        $writeProperties = function (\Exception $err = null, string $arg = null, int $type = self::ARG_EMPTY) use (
            $name,
            $trowError
        ) {
            if ($trowError && $err !== null) {
                throw $err;
            }
            $this->directive = $name;
            $this->argument = $arg;
            $this->argumentType = $type;
            $this->error = $err;
            return $this->error === null;
        };

        if ($argument === null && ($argumentType & self::ARG_EMPTY) === self::ARG_EMPTY) {
            return $writeProperties();
        } elseif ($argumentType === self::ARG_EMPTY) {
            if ($argument !== null) {
                return $writeProperties(new InvalidArgumentException("{$name} directive should not have an argument"));
            }
            return $writeProperties();
        } elseif (($argumentType & self::ARG_HEADERS_LIST) === self::ARG_HEADERS_LIST) {
            # Validate headers list
            $argument = $argument === null ? null : trim($argument);
            if ($argument === null || preg_match('/^[\\w\\-]+(?:(?:\\s*,\\s*)[\\w\\-]+)*$/', $argument) !== 1) {
                return $writeProperties(
                    new InvalidArgumentException(
                        "{$name} directive should have an argument as a comma separated headers name list"
                    )
                );
            }
            return $writeProperties(null, $argument, self::ARG_HEADERS_LIST);
        } elseif (($argumentType & self::ARG_DELTA_SECONDS) === self::ARG_DELTA_SECONDS) {
            $this->argumentType = self::ARG_DELTA_SECONDS;
            // Validate number
            if ($argument === null || preg_match('/^\\d+$/', $argument) !== 1) {
                return $writeProperties(
                    new InvalidArgumentException("{$name} directive should have numeric argument"),
                    '0',
                    self::ARG_DELTA_SECONDS
                );
            }
            return $writeProperties(null, $argument, self::ARG_DELTA_SECONDS);
        }
        return $writeProperties(null, $argument, $argumentType);
    }
}
