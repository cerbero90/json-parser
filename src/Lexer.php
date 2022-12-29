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
 * @implements IteratorAggregate<int, Token>
 */
final class Lexer implements IteratorAggregate
{
    /**
     * The buffer to yield.
     *
     * @var string
     */
    private string $buffer = '';

    /**
     * Whether the current character is escaped.
     *
     * @var bool
     */
    private bool $isEscaping = false;

    /**
     * Whether the current character belongs to a string.
     *
     * @var bool
     */
    private bool $inString = false;

    /**
     * Instantiate the class.
     *
     * @param Source $source
     */
    public function __construct(private Source $source)
    {
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, Token>
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
    private function inString(string $character): bool
    {
        return ($character == '"' && $this->inString && $this->isEscaping)
            || ($character != '"' && $this->inString)
            || ($character == '"' && !$this->inString);
    }

    /**
     * Yield the given character or buffer it
     *
     * @param string $character
     * @return Generator<int, Token>
     */
    private function yieldOrBufferCharacter(string $character): Generator
    {
        if ($this->inString || !isset(Tokens::BOUNDARIES[$character])) {
            $this->buffer .= $character;
            return;
        }

        if ($this->buffer != '') {
            yield Tokenizer::instance()->toToken($this->buffer);
            $this->buffer = '';
        }

        if (isset(Tokens::DELIMITERS[$character])) {
            yield Tokenizer::instance()->toToken($character);
        }
    }
}
