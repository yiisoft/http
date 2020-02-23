<?php

namespace Yiisoft\Http;

use InvalidArgumentException;
use Yiisoft\Http\Header\DefaultHeader;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\ListedValues;
use Yiisoft\Http\Header\WithParams;

class HeaderValues
{
    private string $headerClass;
    private string $headerName;
    /** @var Header[] */
    private array $collection = [];

    private bool $listedValues = false;
    private bool $withParams = false;

    public function __construct(string $headerClass)
    {
        $this->headerClass = $headerClass;
        if (class_exists($headerClass)) {
            if (!is_subclass_of($headerClass, Header::class, true)) {
                throw new InvalidArgumentException("{$headerClass} is not a header.");
            }
            if (is_a($headerClass, DefaultHeader::class, true)) {
                throw new InvalidArgumentException("{$headerClass} has no header name.");
            }
            $this->headerName = $headerClass::NAME;
            $this->listedValues = is_subclass_of($headerClass, ListedValues::class, true);
            $this->withParams = is_subclass_of($headerClass, WithParams::class, true);
        } else {
            $this->headerName = $headerClass;
            $this->headerClass = DefaultHeader::class;
        }
    }

    /**
     * @param Header[]|string[] $headers
     * @param string $headerClass
     * @return static
     * @throws InvalidArgumentException
     */
    public static function createFromArray(array $headers, string $headerClass): self
    {
        $collection = new static($headerClass);
        foreach ($headers as $header) {
            $collection->add($header);
        }
        return $collection;
    }

    public function getName(): string
    {
        return $this->headerName;
    }

    /**
     * @param string|Header $value
     * @return $this
     */
    public function add($value): self
    {
        if ($value instanceof Header) {
            if (get_class($value) !== $this->headerClass) {
                throw new InvalidArgumentException(
                    sprintf('The value must be an instance of %s, %s given', $this->headerClass, get_class($value))
                );
            }
            $this->collection[] = $value;
            return $this;
        }
        if (is_string($value)) {
            $this->collection[] = new $this->headerClass($value);
            return $this;
        }
        throw new InvalidArgumentException(sprintf('The value must be an instance of %s or string', $this->headerClass));
    }

    public function getValues(): array
    {
        $result = [];
        foreach ($this->collection as $header) {
            $result[] = $header->getValue();
        }
        return $result;
    }
}
