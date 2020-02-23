<?php

namespace Yiisoft\Http\Header;

class Accept extends Header implements WithQualityParam
{
    public const NAME = 'Accept';

    public function getParams(): iterable
    {
        return parent::getParams();
    }

    public function withParams(array $params)
    {
        return parent::withParams($params);
    }

    public function getQuality(): string
    {
        return parent::getQuality();
    }
}
