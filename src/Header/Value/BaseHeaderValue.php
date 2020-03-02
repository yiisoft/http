<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Exception;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\WithParams;
use Yiisoft\Http\Header\WithQualityParam;

abstract class BaseHeaderValue
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
    protected ?Exception $error = null;
    protected const HTTP_DATETIME_FORMAT = 'D, d M Y H:i:s \\G\\M\\T';

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

    public static function createHeader(): Header
    {
        return new Header(static::class);
    }

    public function withValue(string $value): self
    {
        $clone = clone $this;
        $clone->value = $value;
        return $clone;
    }
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * It makes sense to use only for HeaderValues that implement the WithParams interface
     * @param array<string, string> $params
     * @return $this
     */
    public function withParams(array $params): self
    {
        $clone = clone $this;
        if (!$this instanceof WithParams) {
            return $clone;
        }
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
    public function getParams(): array
    {
        $result = $this->params;
        if ($this instanceof WithQualityParam) {
            $result['q'] = $this->quality;
        }
        return $result;
    }
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
    public function hasError(): bool
    {
        return $this->error !== null;
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