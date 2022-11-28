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
     * Whether the current character is escaped.
     *
     * @var bool
     */
    protected bool $isEscaping = false;

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
     * @return Generator<int, Token>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->source as $chunk) {
            for ($i = 0, $size = strlen($chunk); $i < $size; $i++) {
                $character = $chunk[$i];
                $this->inString = $this->inString($character);
                $this->isEscaping = $character == '\\' && !$this->isEscaping;

                yield from $this->yieldOrBufferCharacter($character);
            }
        }
    }

    /**
     * Determine whether the given character is within a string
     *
     * @param string $character
     * @return bool
     */
    protected function inString(string $character): bool
    {
        return ($character == '"' && $this->inString && $this->isEscaping)
            || ($character != '"' && $this->inString)
            || ($character == '"' && !$this->inString);
    }

    /**
     * Yield the given character or buffer it
     *
     * @param string $character
     * @return Generator
     */
    protected function yieldOrBufferCharacter(string $character): Generator
    {
        if ($this->inString || !isset(Tokens::BOUNDARIES[$character])) {
            $this->buffer .= $character;
            return;
        }

        if ($this->buffer != '') {
            yield $this->tokenizer->toToken($this->buffer);
            $this->buffer = '';
        }

        if (isset(Tokens::DELIMITERS[$character])) {
            yield $this->tokenizer->toToken($character);
        }
    }
}
