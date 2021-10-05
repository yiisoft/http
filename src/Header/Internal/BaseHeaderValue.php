<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Internal;

use Exception;
use Psr\Http\Message\MessageInterface;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\Parser\HeaderParsingParams;

abstract class BaseHeaderValue
{
    public const NAME = null;

    protected string $value;
    protected ?Exception $error = null;

    protected const PARSING_LIST = false;
    protected const PARSING_Q_PARAM = false;
    protected const PARSING_PARAMS = false;

    public function __construct(string $value = '')
    {
        $this->setValue($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    final public function inject(MessageInterface $message, bool $replace = true): MessageInterface
    {
        if (static::NAME === null) {
            throw new \RuntimeException('Can not inject unnamed header value');
        }
        if ($replace) {
            $message = $message->withoutHeader(static::NAME);
        }
        return $message->withAddedHeader(static::NAME, $this->__toString());
    }

    public static function createHeader(): Header
    {
        return new Header(static::class);
    }

    public static function extract(MessageInterface $message): Header
    {
        return (new Header(static::class))->extract($message);
    }

    public function withValue(string $value): self
    {
        $clone = clone $this;
        $clone->setValue($value);
        return $clone;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param Exception|null $error
     */
    final public function withError(?Exception $error): self
    {
        $clone = clone $this;
        $clone->error = $error;
        return $clone;
    }

    final public function hasError(): bool
    {
        return $this->error !== null;
    }

    final public function getError(): ?Exception
    {
        return $this->error;
    }

    final public static function getParsingParams(): HeaderParsingParams
    {
        $params = new HeaderParsingParams();
        $params->directives = is_subclass_of(static::class, DirectivesHeaderValue::class, true);
        $params->valuesList = $params->directives || static::PARSING_LIST;
        $params->withParams = $params->directives || static::PARSING_PARAMS;
        $params->q = static::PARSING_Q_PARAM;
        return $params;
    }

    protected function setValue(string $value): void
    {
        $this->value = $value;
    }

    final protected function encodeQuotedString(string $string): string
    {
        return preg_replace('/([\\\\"])/', '\\\\$1', $string);
    }

    final protected function validateDateTime(string $value): bool
    {
        return preg_match(
            '/^\\w{3,}, [0-3]?\\d[ \\-]\\w{3}[ \\-]\\d+ [0-2]\\d:[0-5]\\d:[0-5]\\d \\w+|'
                . '\\w{3} \\w{3} [0-3]?\\d [0-2]\\d:[0-5]\\d:[0-5]\\d \\d+$/i',
            trim($value)
        ) === 1;
    }
}
