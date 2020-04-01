<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Condition;

use Yiisoft\Http\Header\ParsingException;
use Yiisoft\Http\Header\Internal\BaseHeaderValue;

/**
 * @link https://tools.ietf.org/html/rfc7232#section-2.3
 */
final class ETag extends BaseHeaderValue
{
    public const NAME = 'ETag';

    private string $tag = '';
    private bool $toStringFromTag = false;
    private bool $weak = true;

    public function __toString(): string
    {
        if ($this->toStringFromTag) {
            return ($this->weak ? 'W/' : '') . '"' . $this->tag . '"';
        } else {
            return $this->value;
        }
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function isWeak(): bool
    {
        return $this->weak;
    }

    public function withTag(string $tag, bool $weak = true): self
    {
        $clone = clone $this;
        $clone->tag = $tag;
        $clone->weak = $weak;
        $clone->toStringFromTag = true;
        return $clone;
    }

    protected function setValue(string $value): void
    {
        $this->value = trim($value);
        $this->toStringFromTag = false;
        if (preg_match('/^(?<weak>W\\/)?"(?<etagc>[^"\\x00-\\x20\\x7F]+)"$/', $this->value, $matches) === 1) {
            $this->tag = $matches['etagc'];
            $this->weak = $matches['weak'] === 'W/';
            $this->error = null;
        } else {
            $this->error = new ParsingException($value, 0, 'Invalid ETag value format', 0, $this->error);
        }
    }
}
