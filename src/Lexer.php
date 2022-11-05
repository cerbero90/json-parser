<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Sources\Source;
use Cerbero\JsonParser\Tokens\Token;
use Cerbero\JsonParser\Tokens\Tokenizer;
use Cerbero\JsonParser\Tokens\Tokens;
use Generator;
use IteratorAggregate;
use Traversable;

/**
 * The JSON lexer.
 *
 */
class Lexer implements IteratorAggregate
{
    /**
     * The tokenizer.
     *
     * @var Tokenizer
     */
    protected Tokenizer $tokenizer;

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
    protected bool $isEscape = false;

    /**
     * Whether the current character belongs to a string.
     *
     * @var bool
     */
    protected bool $inString = false;

    /**
     * Instantiate the class.
     *
     * @param Source $source
     */
    public function __construct(protected Source $source)
    {
        $this->tokenizer = new Tokenizer();
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Token[]
     */
    public function getIterator(): Traversable
    {
        foreach ($this->source as $chunk) {
            for ($i = 0, $size = strlen($chunk); $i < $size; $i++) {
                $character = $chunk[$i];
                $this->inString = $character == '"' && !$this->isEscape && !$this->inString;
                $this->isEscape = $character == '\\' && !$this->isEscape;

                yield from $this->yieldOrBufferCharacter($character);
            }
        }

        if ($this->buffer != '') {
            // @todo test whether this is ever called
            yield $this->tokenizer->toToken($this->buffer);
            $this->buffer = '';
        }
    }

    /**
     * Yield the given character or buffer it
     *
     * @param string $character
     * @return Generator
     */
    protected function yieldOrBufferCharacter(string $character): Generator
    {
        if (isset(Tokens::BOUNDARIES[$character]) && !$this->inString) {
            if ($this->buffer != '') {
                yield $this->tokenizer->toToken($this->buffer);
                $this->buffer = '';
            }

            if (isset(Tokens::DELIMITERS[$character])) {
                yield $this->tokenizer->toToken($character);
            }
        } elseif (!$this->isEscape) {
            $this->buffer .= $character;
        }
    }
}
