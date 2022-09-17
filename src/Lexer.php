<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Sources\Source;
use IteratorAggregate;
use Traversable;

/**
 * The JSON lexer.
 *
 */
class Lexer implements IteratorAggregate
{
    /**
     * The JSON token boundaries.
     *
     * @var array
     */
    protected const BOUNDARIES = [
        "\xEF" => true,
        "\xBB" => true,
        "\xBF" => true,
        "\n" => true,
        "\r" => true,
        "\t" => true,
        ' ' => true,
        '{' => true,
        '}' => true,
        '[' => true,
        ']' => true,
        ':' => true,
        ',' => true,
    ];

    /**
     * The JSON structural boundaries.
     *
     * @var array
     */
    protected const STRUCTURES = [
        '{' => true,
        '}' => true,
        '[' => true,
        ']' => true,
        ':' => true,
        ',' => true,
    ];

    /**
     * The buffer to yield.
     *
     * @var string
     */
    protected string $buffer = '';

    /**
     * Whether the current character is an escape.
     *
     * @var bool
     */
    // protected bool $isEscape = false;

    /**
     * Instantiate the class.
     *
     * @param Source $source
     */
    public function __construct(protected Source $source)
    {
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        foreach ($this->source as $chunk) {
            foreach (mb_str_split($chunk) as $char) {
                // $this->isEscape = $char == '\\' && !$this->isEscape;

                if (isset(static::BOUNDARIES[$char]) && $this->buffer != '') {
                    if (isset(static::STRUCTURES[$char])) {
                        $this->buffer .= $char;
                    }

                    yield $this->buffer;
                    $this->buffer = '';
                    continue;
                }

                // if (!$this->isEscape) {
                $this->buffer .= $char;
                // }
            }
        }

        if ($this->buffer != '') {
            // @todo test whether this is ever called
            yield $this->buffer;
            $this->buffer = '';
        }
    }
}
