<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value;

use Exception;
use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\Rule\WithParams;
use Yiisoft\Http\Header\Rule\WithQualityParam;
use Yiisoft\Http\Header\SortableHeader;

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
        $this->setValue($value);
    }
    public function __toString(): string
    {
        $params = [];
        if ($this instanceof WithParams) {
            foreach ($this->getParams() as $key => $value) {
                if ($key === 'q' && $this instanceof WithQualityParam) {
                    if ($value === '1') {
                        continue;
                    }
                }
                $escaped = $this->encodeQuotedString($value);
                $quoted = $value === ''
                    || strlen($escaped) !== strlen($value)
                    || preg_match('/[\\s,;()\\/:<=>?@\\[\\\\\\]{}]/', $value) === 1;
                $params[] = $key . '=' . ($quoted ? "\"{$escaped}\"" : $value);
            }
        }
        return $this->value === '' ? implode(';', $params) : implode(';', [$this->value, ...$params]);
    }

    public static function createHeader(): Header
    {
        $class = is_subclass_of(static::class, WithQualityParam::class) ? SortableHeader::class : Header::class;
        return new $class(static::class);
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
     * It makes sense to use only for HeaderValues that implement the WithParams interface
     * @param array<string, string> $params
     * @return $this
     */
    public function withParams(array $params): self
    {
        $clone = clone $this;
        $clone->setParams($params);
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
    protected function setValue(string $value): void
    {
        $this->value = $value;
    }
    protected function setParams(array $params): void
    {
        $this->params = [];
        foreach ($params as $key => $value) {
            # todo decide: what about numeric keys?
            $key = strtolower($key);
            if (!key_exists($key, $this->params)) {
                $this->params[$key] = $value;
            }
        }
        if ($this instanceof WithQualityParam) {
            if (key_exists('q', $this->params)) {
                $this->setQuality($this->params['q']);
                unset($this->params['q']);
            } else {
                $this->setQuality('1');
            }
        }
    }
    protected function encodeQuotedString(string $string): string
    {
        return preg_replace('/([\\\\"])/', '\\\\$1', $string);
    }
}
