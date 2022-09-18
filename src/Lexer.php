<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Sources\Source;
use Cerbero\JsonParser\Tokens\Tokens;
use IteratorAggregate;
use Traversable;

/**
 * The JSON lexer.
 *
 */
class Lexer implements IteratorAggregate
{
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

                if (isset(Tokens::BOUNDARIES[$char]) && $this->buffer != '') {
                    yield $this->buffer;
                    $this->buffer = '';

                    if (isset(Tokens::STRUCTURES[$char])) {
                        yield $char;
                    }
                } else {
                    // if (!$this->isEscape) {
                    $this->buffer .= $char;
                    // }
                }
            }
        }

        if ($this->buffer != '') {
            // @todo test whether this is ever called
            yield $this->buffer;
            $this->buffer = '';
        }
    }
}
