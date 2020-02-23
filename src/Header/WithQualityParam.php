<?php

namespace Yiisoft\Http\Header;

interface WithQualityParam extends ListedValues, WithParams
{
    public function getQuality();
}
