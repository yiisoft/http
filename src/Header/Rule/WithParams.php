<?php

namespace Yiisoft\Http\Header\Rule;

interface WithParams
{
    /**
     * @return array<string, string>
     */
    public function getParams(): array;
    /**
     * @param array<string, string> $params
     * @return $this
     */
    public function withParams(array $params);
}
