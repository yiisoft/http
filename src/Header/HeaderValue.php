<?php

namespace Yiisoft\Http\Header;

abstract class HeaderValue
{
    public const NAME = null;

    protected string $value;
    /** @var array<string, string> */
    private array $params = [];
    private string $quality = '1';
    private ?string $error = null;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        $params = [];
        foreach ($this->params as $key => $value) {
            $escaped = preg_replace('/(\\\\.)/', '\\$1', $value);
            $params[] = $key . '=' . (strlen($escaped) > strlen($value) ? "\"{$escaped}\"" : $value);
        }
        return $this->value === '' ? implode(';', $params) : implode(';', [$this->value, ...$params]);
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
    public function withParams(array $params)
    {
        $clone = clone $this;
        $clone->params = $params;
        if ($clone instanceof WithQualityParam) {
            $clone->quality = $params['q'] ?? '1';
        }
        return $clone;
    }
    public function getParams(): iterable
    {
        return $this->params;
    }
    // with quality
    public function getQuality(): string
    {
        return $this->quality;
    }
    public function withError(string $error)
    {
        $clone = clone $this;
        $clone->error = $error;
        return $clone;
    }
    public function getError(): ?string
    {
        return $this->error;
    }
}
