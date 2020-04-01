<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Internal;

use Yiisoft\Http\Header\Header;
use Yiisoft\Http\Header\SortableHeader;

abstract class WithParamsHeaderValue extends BaseHeaderValue
{
    /**
     * @see WithParams
     * @var array<string, string>
     */
    private array $params = [];
    /**
     * @link https://tools.ietf.org/html/rfc7231#section-5.3.1
     * @var string value between 0.000 and 1.000
     */
    private string $quality = '1';

    protected const PARSING_PARAMS = true;

    public function __toString(): string
    {
        $params = [];
        foreach ($this->getParams() as $key => $value) {
            if ($key === 'q' && static::PARSING_Q_PARAM) {
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
        return $this->value === '' ? implode(';', $params) : implode(';', [$this->value, ...$params]);
    }

    public static function createHeader(): Header
    {
        $class = static::PARSING_Q_PARAM ? SortableHeader::class : Header::class;
        return new $class(static::class);
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
        if (static::PARSING_Q_PARAM) {
            $result['q'] = $this->quality;
        }
        return $result;
    }
    public function getQuality(): string
    {
        return $this->quality;
    }

    protected function setQuality(string $q): bool
    {
        if (preg_match('/^0(?:\\.\\d{1,3})?$|^1(?:\\.0{1,3})?$/', $q) !== 1) {
            return false;
        }
        $this->quality = rtrim($q, '0.') ?: '0';
        return true;
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
        if (static::PARSING_Q_PARAM) {
            if (key_exists('q', $this->params)) {
                $this->setQuality($this->params['q']);
                unset($this->params['q']);
            } else {
                $this->setQuality('1');
            }
        }
    }
}
