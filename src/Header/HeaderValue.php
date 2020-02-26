<?php

namespace Yiisoft\Http\Header;

use Exception;

abstract class HeaderValue
{
    public const NAME = null;

    protected string $value;
    /** @var array<string, string> */
    private array $params = [];
    private string $quality = '1';
    private ?Exception $error = null;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        $params = [];
        foreach ($this->params as $key => $value) {
            $escaped = preg_replace('/([\\\\"])/', '\\\\$1', $value);
            $quoted = $value === '' || strpos(' ', $escaped) !== false || strlen($escaped) > strlen($value);
            $params[] = $key . '=' . ($quoted ? "\"{$escaped}\"" : $value);
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
    public function withError(?Exception $error)
    {
        $clone = clone $this;
        $clone->error = $error;
        return $clone;
    }
    public function getError(): ?Exception
    {
        return $this->error;
    }
}
