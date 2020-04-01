<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Yiisoft\Http\Header\Parser\HeaderParsingParams;
use Yiisoft\Http\Header\Parser\ValueFieldParser;
use Yiisoft\Http\Header\Value\Unnamed\SimpleValue;
use Yiisoft\Http\Header\Internal\BaseHeaderValue;

class Header implements \IteratorAggregate, \Countable
{
    protected string $headerClass;
    protected string $headerName;
    /** @var BaseHeaderValue[] */
    protected array $collection = [];

    protected const DEFAULT_VALUE_CLASS = SimpleValue::class;

    /**
     * @param string $nameOrClass Header name or header value class
     */
    public function __construct(string $nameOrClass)
    {
        $this->headerClass = $nameOrClass;
        if (class_exists($nameOrClass)) {
            if (!is_subclass_of($nameOrClass, BaseHeaderValue::class, true)) {
                throw new InvalidArgumentException("{$nameOrClass} is not a header.");
            }
            if (empty($nameOrClass::NAME)) {
                throw new InvalidArgumentException("{$nameOrClass} has no header name.");
            }
            $this->headerName = $nameOrClass::NAME;
        } else {
            if ($nameOrClass === '') {
                throw new InvalidArgumentException("Empty header name.");
            }
            $this->headerName = $nameOrClass;
            $this->headerClass = static::DEFAULT_VALUE_CLASS;
        }
    }

    public function getIterator(): iterable
    {
        return $this->getValues(true);
    }
    public function count(): int
    {
        return count($this->collection);
    }

    public function getName(): string
    {
        return $this->headerName;
    }
    public function getValueClass(): string
    {
        return $this->headerClass;
    }
    /**
     * @param bool $ignoreIncorrect
     * @return BaseHeaderValue[]
     */
    public function getValues(bool $ignoreIncorrect = true): array
    {
        $result = [];
        foreach ($this->collection as $header) {
            if ($ignoreIncorrect && $header->hasError()) {
                continue;
            }
            $result[] = $header;
        }
        return $result;
    }
    /**
     * @param bool $ignoreIncorrect
     * @return string[]
     */
    public function getStrings(bool $ignoreIncorrect = true): array
    {
        $result = [];
        foreach ($this->collection as $header) {
            if ($ignoreIncorrect && $header->hasError()) {
                continue;
            }
            $result[] = $header->__toString();
        }
        return $result;
    }

    /**
     * @param string[]|BaseHeaderValue[] $values
     * @return $this
     */
    public function withValues(array $values): self
    {
        $clone = clone $this;
        foreach ($values as $value) {
            $clone->addValue($value);
        }
        return $clone;
    }
    /**
     * @param string|BaseHeaderValue $value
     * @return $this
     */
    public function withValue($value): self
    {
        $clone = clone $this;
        $clone->addValue($value);
        return $clone;
    }

    /**
     * Export header values into HTTP message
     * @param MessageInterface $message Request or Response instance
     * @param bool $replace Replace existing headers
     * @param bool $ignoreIncorrect Don't export values that have error
     * @return MessageInterface
     */
    public function inject(
        MessageInterface $message,
        bool $replace = true,
        bool $ignoreIncorrect = true
    ): MessageInterface {
        if ($replace) {
            $message = $message->withoutHeader($this->headerName);
        }
        foreach ($this->collection as $value) {
            if ($ignoreIncorrect && $value->hasError()) {
                continue;
            }
            $message = $message->withAddedHeader($this->headerName, $value->__toString());
        }
        return $message;
    }
    /**
     * Import header values from HTTP message
     * @param MessageInterface $message Request or Response instance
     * @return $this
     */
    public function extract(MessageInterface $message): self
    {
        #todo test it
        return $this->withValues($message->getHeader($this->headerName));
    }

    /**
     * @param string|BaseHeaderValue $value
     */
    protected function addValue($value): void
    {
        if ($value instanceof BaseHeaderValue) {
            if (get_class($value) !== $this->headerClass) {
                throw new InvalidArgumentException(
                    sprintf('The value must be an instance of %s, %s given', $this->headerClass, get_class($value))
                );
            }
            $this->collect($value);
            return;
        }
        if (is_string($value)) {
            $this->parseAndCollect($value);
            return;
        }
        throw new InvalidArgumentException(
            sprintf('The value must be an instance of %s or string', $this->headerClass)
        );
    }
    protected function collect(BaseHeaderValue $value): void
    {
        $this->collection[] = $value;
    }
    private function parseAndCollect(string $body): void
    {
        /**
         * @var HeaderParsingParams $parsingParams
         * @see \Yiisoft\Http\Header\Internal\BaseHeaderValue::getParsingParams
         */
        $parsingParams = $this->headerClass::getParsingParams();

        // var_dump($body, $this->headerClass, $parsingParams); die;
        foreach (ValueFieldParser::parse($body, $this->headerClass, $parsingParams) as $value) {
            $this->collect($value);
        }
    }
}
