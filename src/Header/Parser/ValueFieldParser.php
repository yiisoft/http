<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Parser;

use Generator;
use InvalidArgumentException;
use Yiisoft\Http\Header\Internal\BaseHeaderValue;
use Yiisoft\Http\Header\Internal\DirectivesHeaderValue;

final class ValueFieldParser
{
    // Parsing's constants
    private const
        DELIMITERS = '"(),/:;<=>?@[\\]{}',
        READ_NONE = 0,
        READ_VALUE = 1,
        READ_PARAM_NAME = 2,
        READ_PARAM_QUOTED_VALUE = 3,
        READ_PARAM_VALUE = 4;

    /**
     * @psalm-param class-string<BaseHeaderValue> $class
     * @psalm-return Generator<int, BaseHeaderValue, void, void>
     */
    public static function parse(string $body, string $class, HeaderParsingParams $params): Generator
    {
        if (!is_a($class, BaseHeaderValue::class, true)) {
            throw new InvalidArgumentException('$class should be instance of BaseHeaderValue.');
        }
        if (!$params->valuesList && !$params->withParams && !$params->directives) {
            yield new $class(trim($body));
            return;
        }

        $state = new ValueFieldState();
        $state->part = self::READ_VALUE;
        try {
            /** @link https://tools.ietf.org/html/rfc7230#section-3.2.6 */
            for ($pos = 0, $length = strlen($body); $pos < $length; ++$pos) {
                $sym = $body[$pos];
                if ($state->part === self::READ_VALUE) {
                    if ($sym === '=' && $params->withParams) {
                        $state->key = ltrim($state->buffer);
                        $state->buffer = '';
                        if (preg_match('/\s/', $state->key) === 0) {
                            $state->part = self::READ_PARAM_VALUE;
                            continue;
                        }
                        $state->key = preg_replace('/\s+/', ' ', $state->key);
                        $chunks = explode(' ', $state->key);
                        if (count($chunks) > 2 || preg_match('/\s$/', $state->key) === 1) {
                            array_pop($chunks);
                            $state->buffer = implode(' ', $chunks);
                            throw new ParsingException($body, $pos, 'Syntax error');
                        }
                        $state->part = self::READ_PARAM_VALUE;
                        [$state->value, $state->key] = $chunks;
                    } elseif ($sym === ';' && $params->withParams) {
                        $state->part = self::READ_PARAM_NAME;
                        $state->value = trim($state->buffer);
                        $state->buffer = '';
                    } elseif ($sym === ',' && $params->valuesList) {
                        $state->value = trim($state->buffer);
                        yield self::createHeaderValue($class, $params, $state);
                    } else {
                        $state->buffer .= $sym;
                    }
                    continue;
                }
                if ($state->part === self::READ_PARAM_NAME) {
                    if ($sym === '=') {
                        $state->key = $state->buffer;
                        $state->buffer = '';
                        $state->part = self::READ_PARAM_VALUE;
                    } elseif (strpos(self::DELIMITERS, $sym) !== false) {
                        throw new ParsingException($body, $pos, 'Delimiter char in a param name');
                    } elseif (ord($sym) <= 32) {
                        if ($state->buffer !== '') {
                            throw new ParsingException($body, $pos, 'Space in a param name');
                        }
                    } else {
                        $state->buffer .= $sym;
                    }
                    continue;
                }
                if ($state->part === self::READ_PARAM_VALUE) {
                    if ($state->buffer === '') {
                        if ($sym === '"') {
                            $state->part = self::READ_PARAM_QUOTED_VALUE;
                        } elseif (ord($sym) <= 32) {
                            continue;
                        } elseif (strpos(self::DELIMITERS, $sym) === false) {
                            $state->buffer .= $sym;
                        } else {
                            throw new ParsingException($body, $pos, 'Delimiter char in a unquoted param value');
                        }
                    } elseif (ord($sym) <= 32) {
                        $state->part = self::READ_NONE;
                        $state->addParamFromBuffer();
                    } elseif (strpos(self::DELIMITERS, $sym) === false) {
                        $state->buffer .= $sym;
                    } elseif ($sym === ';') {
                        $state->part = self::READ_PARAM_NAME;
                        $state->addParamFromBuffer();
                    } elseif ($sym === ',' && $params->valuesList) {
                        $state->part = self::READ_VALUE;
                        $state->addParamFromBuffer();
                        yield self::createHeaderValue($class, $params, $state);
                    } else {
                        $state->buffer = '';
                        throw new ParsingException($body, $pos, 'Delimiter char in a unquoted param value');
                    }
                    continue;
                }
                if ($state->part === self::READ_PARAM_QUOTED_VALUE) {
                    if ($sym === '\\') { // quoted pair
                        if (++$pos >= $length) {
                            throw new ParsingException($body, $pos, 'Incorrect quoted pair');
                        }
                        $state->buffer .= $body[$pos];
                    } elseif ($sym === '"') { // end
                        $state->part = self::READ_NONE;
                        $state->addParamFromBuffer();
                    } else {
                        $state->buffer .= $sym;
                    }
                    continue;
                }
                if ($state->part === self::READ_NONE) {
                    if (ord($sym) <= 32) {
                        continue;
                    }
                    if ($sym === ';' && $params->withParams) {
                        $state->part = self::READ_PARAM_NAME;
                    } elseif ($sym === ',' && $params->valuesList) {
                        $state->part = self::READ_VALUE;
                        yield self::createHeaderValue($class, $params, $state);
                    } else {
                        throw new ParsingException($body, $pos, 'Expected Separator');
                    }
                }
            }
        } catch (ParsingException $e) {
            $state->error = $e;
        }
        if ($state->part === self::READ_VALUE) {
            $state->value = trim($state->buffer);
        } elseif (in_array($state->part, [self::READ_PARAM_VALUE, self::READ_PARAM_QUOTED_VALUE], true)) {
            if ($state->buffer === '') {
                $state->error = $state->error ?? new ParsingException($body, $pos, 'Empty value should be quoted');
            } else {
                $state->addParamFromBuffer();
            }
        }
        yield self::createHeaderValue($class, $params, $state);
    }

    /**
     * @psalm-param class-string<BaseHeaderValue> $class
     */
    protected static function createHeaderValue(
        string $class,
        HeaderParsingParams $params,
        ValueFieldState $state
    ): BaseHeaderValue {
        /** @var BaseHeaderValue|DirectivesHeaderValue $item */
        $item = new $class($state->value);
        if ($params->directives && $item instanceof DirectivesHeaderValue) {
            if ($state->value === '' && count($state->params) > 0) {
                $item = $item->withDirective(key($state->params), current($state->params));
            }
        } elseif ($params->withParams) {
            $item = $item->withParams($state->params);
        }
        if ($state->error !== null) {
            $item = $item->withError($state->error);
        }
        $state->clear();
        return $item;
    }
}
