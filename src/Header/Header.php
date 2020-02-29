<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Yiisoft\Http\Header\Value\DefaultValue;
use Yiisoft\Http\Header\Value\BaseHeaderValue;

final class Header implements \IteratorAggregate, \Countable
{
    private string $headerClass;
    private string $headerName;
    /** @var BaseHeaderValue[] */
    private array $collection = [];

    private bool $listedValues = false;
    private bool $withParams = false;

    // Parsing's constants
    private const
        DELIMITERS = '"(),/:;<=>?@[\\]{}',
        READ_NONE = 0,
        READ_VALUE = 1,
        READ_PARAM_NAME = 2,
        READ_PARAM_QUOTED_VALUE = 3,
        READ_PARAM_VALUE = 4;

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
            $this->listedValues = is_subclass_of($nameOrClass, ListedValues::class, true);
            $this->withParams = is_subclass_of($nameOrClass, WithParams::class, true);
        } else {
            if ($nameOrClass === '') {
                throw new InvalidArgumentException("Empty header name.");
            }
            $this->headerName = $nameOrClass;
            $this->headerClass = DefaultValue::class;
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
    /**
     * @param BaseHeaderValue[]|string[] $values
     * @param string                     $headerClass
     * @return static
     * @throws InvalidArgumentException
     */
    public static function createFromArray(array $values, string $headerClass): self
    {
        return (new static($headerClass))->addArray($values);
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
    public function getValues($ignoreIncorrect = true): array
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
    public function getStrings($ignoreIncorrect = true): array
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
    public function addArray(array $values): self
    {
        foreach ($values as $value) {
            $this->add($value);
        }
        return $this;
    }
    /**
     * @param string|BaseHeaderValue $value
     * @return $this
     */
    public function add($value): self
    {
        if ($value instanceof BaseHeaderValue) {
            if (get_class($value) !== $this->headerClass) {
                throw new InvalidArgumentException(
                    sprintf('The value must be an instance of %s, %s given', $this->headerClass, get_class($value))
                );
            }
            $this->collection[] = $value;
            return $this;
        }
        if (is_string($value)) {
            $this->parseAndCollect($value);
            return $this;
        }
        throw new InvalidArgumentException(
            sprintf('The value must be an instance of %s or string', $this->headerClass)
        );
    }

    public function inject(MessageInterface $message, bool $replace = true, bool $ignoreIncorrect = true): MessageInterface
    {
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

    private function parseAndCollect(string $body): void
    {
        if (!$this->listedValues && !$this->withParams) {
            $this->collection[] = new $this->headerClass(trim($body));
            return;
        }
        $part = self::READ_VALUE;
        $buffer = '';
        $key = '';
        $value = '';
        $params = [];
        $error = null;
        $addParam = static function ($key, $value) use (&$params) {
            if (!key_exists($key, $params)) {
                $params[$key] = $value;
            }
        };
        $collectHeaderValue = function () use (&$key, &$value, &$buffer, &$params, &$added, &$error) {
            /** @var BaseHeaderValue $item */
            $item = new $this->headerClass($value);
            if ($this->withParams) {
                $item = $item->withParams($params);
            }
            if ($error !== null) {
                $item = $item->withError($error);
            }
            $this->collection[] = $item;
            $key = $buffer = $value = '';
            $params = [];
            ++$added;
        };
        $added = 0;
        try {
            /** @see https://tools.ietf.org/html/rfc7230#section-3.2.6 */
            for ($pos = 0, $length = strlen($body); $pos < $length; ++$pos) {
                $s = $body[$pos];
                if ($part === self::READ_VALUE) {
                    if ($s === '=' && $this->withParams) {
                        $key = ltrim($buffer);
                        $buffer = '';
                        if (preg_match('/\s/', $key) === 0) {
                            $part = self::READ_PARAM_VALUE;
                            continue;
                        }
                        $key = preg_replace('/\s+/', ' ', $key);
                        $chunks = explode(' ', $key);
                        if (count($chunks) > 2 || preg_match('/\s$/', $key) === 1) {
                            array_pop($chunks);
                            $buffer = implode(' ', $chunks);
                            throw new ParsingException($body, $pos, 'Syntax error');
                        }
                        $part = self::READ_PARAM_VALUE;
                        [$value, $key] = $chunks;
                    } elseif ($s === ';' && $this->withParams) {
                        $part = self::READ_PARAM_NAME;
                        $value = trim($buffer);
                        $buffer = '';
                    } elseif ($s === ',' && $this->listedValues) {
                        $value = trim($buffer);
                        $collectHeaderValue();
                    } else {
                        $buffer .= $s;
                    }
                    continue;
                }
                if ($part === self::READ_PARAM_NAME) {
                    if ($s === '=') {
                        $key = $buffer;
                        $buffer = '';
                        $part = self::READ_PARAM_VALUE;
                    } elseif (strpos(self::DELIMITERS, $s) !== false) {
                        throw new ParsingException($body, $pos, 'Delimiter char in a param name');
                    } elseif (ord($s) <= 32) {
                        if ($buffer !== '') {
                            throw new ParsingException($body, $pos, 'Space in a param name');
                        }
                    } else {
                        $buffer .= $s;
                    }
                    continue;
                }
                if ($part === self::READ_PARAM_VALUE) {
                    if ($buffer === '') {
                        if ($s === '"') {
                            $part = self::READ_PARAM_QUOTED_VALUE;
                        } elseif (ord($s) <= 32) {
                            continue;
                        } elseif (strpos(self::DELIMITERS, $s) === false) {
                            $buffer .= $s;
                        } else {
                            throw new ParsingException($body, $pos, 'Delimiter char in a unquoted param value');
                        }
                    } elseif (ord($s) <= 32) {
                        $part = self::READ_NONE;
                        $addParam($key, $buffer);
                        $key = $buffer = '';
                    } elseif (strpos(self::DELIMITERS, $s) === false) {
                        $buffer .= $s;
                    } elseif ($s === ';') {
                        $part = self::READ_PARAM_NAME;
                        $addParam($key, $buffer);
                        $key = $buffer = '';
                    } elseif ($s === ',' && $this->listedValues) {
                        $part = self::READ_VALUE;
                        $addParam($key, $buffer);
                        $collectHeaderValue();
                    } else {
                        $buffer = '';
                        throw new ParsingException($body, $pos, 'Delimiter char in a unquoted param value');
                    }
                    continue;
                }
                if ($part === self::READ_PARAM_QUOTED_VALUE) {
                    if ($s === '\\') { // quoted pair
                        if (++$pos >= $length) {
                            throw new ParsingException($body, $pos, 'Incorrect quoted pair');
                        } else {
                            $buffer .= $body[$pos];
                        }
                    } elseif ($s === '"') { // end
                        $part = self::READ_NONE;
                        $addParam($key, $buffer);
                        $key = $buffer = '';
                    } else {
                        $buffer .= $s;
                    }
                    continue;
                }
                if ($part === self::READ_NONE) {
                    if (ord($s) <= 32) {
                        continue;
                    } elseif ($s === ';' && $this->withParams) {
                        $part = self::READ_PARAM_NAME;
                    } elseif ($s === ',' && $this->listedValues) {
                        $part = self::READ_VALUE;
                        $collectHeaderValue();
                    } else {
                        throw new ParsingException($body, $pos, 'Expected Separator');
                    }
                }
            }
        } catch (ParsingException $e) {
            $error = $e;
        }
        /** @var BaseHeaderValue $item */
        if ($part === self::READ_VALUE) {
            $value = trim($buffer);
        } elseif (in_array($part, [self::READ_PARAM_VALUE, self::READ_PARAM_QUOTED_VALUE], true)) {
            if ($buffer === '') {
                $error = $error ?? new ParsingException($body, $pos, 'Empty value should be quoted');
            } else {
                $addParam($key, $buffer);
            }
        }
        $collectHeaderValue();
    }
}
