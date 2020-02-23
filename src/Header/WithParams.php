<?php

namespace Yiisoft\Http\Header;

interface WithParams
{
    public function getParams(): iterable;
    /**
     * @param array<string, string> $params
     * @return $this
     */
    public function withParams(array $params);
}
