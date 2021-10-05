<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Parser;

use Throwable;

final class ParsingException extends \Exception
{
    private string $value;
    private int $position;

    public function __construct(string $value, int $position, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->value = $value;
        $this->position = $position;
        parent::__construct($message, $code, $previous);
    }
}
