<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Parser;

class ValueFieldState
{
    public int $part = 0;
    public string $buffer = '';
    public string $key = '';
    public string $value = '';
    public array $params = [];

    public ?ParsingException $error = null;

    public function addParam($key, $value): void
    {
        if (!array_key_exists($key, $this->params)) {
            $this->params[$key] = $value;
        }
    }

    public function addParamFromBuffer(): void
    {
        $this->addParam($this->key, $this->buffer);
        $this->key = $this->buffer = '';
    }

    public function clear(): void
    {
        $this->key = $this->buffer = $this->value = '';
        $this->params = [];
        $this->error = null;
    }
}
