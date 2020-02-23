<?php

namespace Yiisoft\Http\Header;

abstract class Header
{
    public const NAME = null;

    protected string $value;
    /** @var array<string, string> */
    private array $params = [];
    private string $quality = '1';

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
    /**
     * @return mixed
     */
    public function getParsedValue()
    {
        return $this->getValue();
    }

    // with params
    protected function withParams(array $params)
    {
        $clone = clone $this;
        $clone->params = $params;
        return $clone;
    }
    protected function getParams(): iterable
    {
        return $this->params;
    }
    // with quality
    protected function getQuality(): string
    {
        return $this->quality;
    }
}
