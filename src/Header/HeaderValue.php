<?php

namespace Yiisoft\Http\Header;

use Exception;

abstract class HeaderValue
{
    public const NAME = null;

    protected string $value;
    /**
     * @see WithParams
     * @var array<string, string>
     */
    private array $params = [];
    /**
     * @see WithQualityParam
     * @var string
     */
    private string $quality = '1';
    private ?Exception $error = null;

    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        $params = [];
        foreach ($this->getParams() as $key => $value) {
            if ($key === 'q' && $this instanceof WithQualityParam) {
                if ($value === '1') {
                    continue;
                }
            }
            $escaped = preg_replace('/([\\\\"])/', '\\\\$1', $value);
            $quoted = $value === ''
                || strlen($escaped) !== strlen($value)
                || preg_match('/[\\s,;()\\/:<=>?@\\[\\\\\\]{}]/', $value) === 1;
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

    /**
     * @param array<string, string> $params
     * @return $this
     * @throws Exception
     */
    public function withParams(array $params): self
    {
        if (!$this instanceof WithParams) {
            #todo: test it
            throw new Exception(sprintf('Method withParams requires %s interface', WithParams::class));
        }
        $clone = clone $this;
        $clone->params = [];
        foreach ($params as $key => $value) {
            $key = strtolower($key);
            if (!key_exists($key, $clone->params)) {
                $clone->params[$key] = $value;
            }
        }
        if ($clone instanceof WithQualityParam) {
            if (key_exists('q', $clone->params)) {
                $clone->setQuality($clone->params['q']);
                unset($clone->params['q']);
            } else {
                $clone->setQuality('1');
            }
        }
        return $clone;
    }
    public function getParams(): iterable
    {
        $result = $this->params;
        if ($this instanceof WithQualityParam) {
            $result['q'] = $this->quality;
        }
        return $result;
    }
    /**
     * @return string value between 0.000 and 1.000
     */
    public function getQuality(): string
    {
        return $this->quality;
    }
    /**
     * @param Exception|null $error
     * @return $this
     */
    public function withError(?Exception $error): self
    {
        $clone = clone $this;
        $clone->error = $error;
        return $clone;
    }
    public function getError(): ?Exception
    {
        return $this->error;
    }
    protected function setQuality(string $q): bool
    {
        if (preg_match('/^0(?:\\.\\d{1,3})?$|^1(?:\\.0{1,3})?$/', $q) !== 1) {
            return false;
        }
        $this->quality = rtrim($q, '0.') ?: '0';
        return true;
    }
}
