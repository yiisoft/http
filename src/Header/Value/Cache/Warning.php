<?php

declare(strict_types=1);

namespace Yiisoft\Http\Header\Value\Cache;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Yiisoft\Http\Header\ParsingException;
use Yiisoft\Http\Header\Value\BaseHeaderValue;

/**
 * @link https://tools.ietf.org/html/rfc7234#section-5.5
 * @link https://developer.mozilla.org/docs/Web/HTTP/Headers/Warning
 */
final class Warning extends BaseHeaderValue
{
    public const NAME = 'Warning';

    /**
     * A cache SHOULD generate this whenever the sent response is stale.
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.1
     */
    public const RESPONSE_IS_STALE = 110;
    /**
     * A cache SHOULD generate this when sending a stale response because an attempt to validate the response failed,
     * due to an inability to reach the server.
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.2
     */
    public const REVALIDATION_FAILED = 111;
    /**
     * A cache SHOULD generate this if it is intentionally disconnected from the rest of the network for a period of
     * time.
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.3
     */
    public const DISCONNECTED_OPERATION = 112;
    /**
     * A cache SHOULD generate this if it heuristically chose a freshness lifetime greater than 24 hours and the
     * response's age is greater than 24 hours.
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.4
     */
    public const HEURISTIC_EXPIRATION = 113;
    /**
     * The warning text can include arbitrary information to be presented to a human user or logged.  A system receiving
     * this warning MUST NOT take any automated action, besides presenting the warning to the user.
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.5
     */
    public const MISCELLANEOUS_WARNING = 199;
    /**
     * This Warning code MUST be added by a proxy if it applies any transformation to the representation, such as
     * changing the content-coding, media-type, or modifying the representation data, unless this Warning code already
     * appears in the response.
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.6
     */
    public const TRANSFORMATION_APPLIED = 214;
    /**
     * The warning text can include arbitrary information to be presented to a human user or logged.  A system receiving
     * this warning MUST NOT take any automated action.
     * @link https://tools.ietf.org/html/rfc7234#section-5.5.7
     */
    public const MISCELLANEOUS_PERSISTENT_WARNING = 299;

    private bool $useDataset = false;
    private int $code;
    private string $agent;
    private string $text;
    private ?DateTimeImmutable $date = null;

    public function __toString(): string
    {
        if ($this->useDataset) {
            $result = "{$this->code} $this->agent \"" . $this->encodeQuotedString($this->text) . "\"";
            if ($this->date !== null) {
                $result .= ' "' . $this->date->format(DateTimeInterface::RFC7231) . '"';
            }
            return $result;
        }
        return parent::__toString();
    }

    public function getCode(): ?int
    {
        return $this->useDataset ? $this->code : null;
    }
    public function getAgent(): ?string
    {
        return $this->useDataset ? $this->agent : null;
    }
    public function getText(): ?string
    {
        return $this->useDataset ? $this->text : null;
    }
    public function getDate(): ?DateTimeImmutable
    {
        return $this->useDataset ? $this->date : null;
    }

    public function withDataset(int $code, string $agent = '-', string $text = '', DateTimeImmutable $date = null): self
    {
        $clone = clone $this;
        $clone->code = $code;
        $clone->agent = $agent;
        $clone->text = $text;
        $clone->date = $date;
        $clone->useDataset = true;
        return $clone;
    }

    protected function setValue(string $value): void
    {
        $value = trim($value);
        $parts = preg_split('/\\s+/', $value, 3);
        $this->resetValues();
        try {
            // code
            if (preg_match('/^[1-9]\\d{2}$/', $parts[0]) !== 1) {
                throw new ParsingException($parts[0], 0, 'Incorrect code value.');
            }
            $this->code = (int)$parts[0];
            // agent
            if (!isset($parts[1])) {
                throw new ParsingException($value, 0, 'Agent value not defined.');
            }
            $this->agent = $parts[1];
            // text
            if (!isset($parts[2])) {
                throw new ParsingException($value, 0, 'Text not defined.');
            }
            if (preg_match(
                    '/^"(?<text>(?:(?:\\\\.)+|[^\\\\"]+)*)"(?:(?:\\s+"(?<date>[a-zA-Z0-9, \\-:]+)")?|\\s*)$/',
                    $parts[2],
                    $matches
                ) !== 1) {
                throw new ParsingException($parts[2], 0, 'Bad quoted string format.');
            }
            $this->text = preg_replace("/\\\\(.)/", '$1', $matches['text']);
            // date
            if (isset($matches['date'])) {
                if (preg_match(
                        '/^\\w{3,}, [0-3]?\\d[ \\-]\\w{3}[ \\-]\\d+ [0-2]\\d:[0-5]\\d:[0-5]\\d \\w+|'
                        . '\\w{3} \\w{3} [0-3]?\\d [0-2]\\d:[0-5]\\d:[0-5]\\d \\d+$/i',
                        $matches['date']
                    ) !== 1) {
                    throw new ParsingException($matches['date'], 0, 'Incorrect datetime format.');
                }
                $this->date = new DateTimeImmutable($matches['date']);
            }
            $this->useDataset = true;
            $this->error = null;
        } catch (\Exception $e) {
            $this->error = $e;
        }
        parent::setValue($value);
    }

    final private function resetValues()
    {
        $this->useDataset = false;
        $this->date = null;
    }
}
