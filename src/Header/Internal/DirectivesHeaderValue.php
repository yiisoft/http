<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Internal;

use InvalidArgumentException;
use Yiisoft\Http\Header\DirectiveHeader;

abstract class DirectivesHeaderValue extends BaseHeaderValue
{
    protected const DIRECTIVES = [];

    protected const ARG_EMPTY = 1;
    protected const ARG_DELTA_SECONDS = 2;
    protected const ARG_HEADERS_LIST = 4;
    protected const ARG_CUSTOM = 8;

    protected string $directive = '';
    protected ?string $argument = null;
    protected int $argumentType = self::ARG_EMPTY;

    public function __toString(): string
    {
        if ($this->directive === '') {
            return '';
        }
        if ($this->argument === null) {
            return $this->directive;
        }
        if ($this->argumentType === self::ARG_HEADERS_LIST) {
            return "{$this->directive}=\"{$this->argument}\"";
        }
        if ($this->argumentType === self::ARG_CUSTOM) {
            $argument = $this->encodeQuotedString($this->argument);
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

    public static function createHeader(): DirectiveHeader
    {
        return new DirectiveHeader(static::class);
    }

    public function getDirective(): string
    {
        return $this->directive;
    }
    public function getValue(): string
    {
        return $this->getDirective();
    }

    public function hasArgument(): bool
    {
        return $this->argument !== null;
    }
    public function getArgument(): ?string
    {
        return $this->argument;
    }
    public function getArgumentList(): array
    {
        return $this->argument === null ? [] : explode(',', $this->argument);
    }

    /**
     * @param string $directive
     * @param string|null $argument
     *
     * @throws InvalidArgumentException
     */
    public function withDirective(string $directive, string $argument = null): self
    {
        $clone = clone $this;
        $clone->setDirective($directive, $argument, true);
        return $clone;
    }

    protected function setValue(string $value): void
    {
        $this->setDirective($value);
    }
    private function setDirective(string $value, string $argument = null, bool $trowError = false): bool
    {
        $name = strtolower($value);

        $argumentType = static::DIRECTIVES[$name] ?? self::ARG_CUSTOM;

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
            // Validate headers list
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
