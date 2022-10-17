<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Sources\Source;
use Cerbero\JsonParser\Tokens\Token;
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
     * The map of token instances.
     *
     * @var array<int, Token>
     */
    protected static array $tokensMap = [];

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
        $this->hydrateTokens();
    }

    /**
     * Set the hydrated tokens
     *
     * @return void
     */
    protected function hydrateTokens(): void
    {
        if (static::$tokensMap) {
            return;
        }

        foreach (Tokens::MAP as $type => $class) {
            static::$tokensMap[$type] = new $class();
        }
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return \Cerbero\JsonParser\Tokens\Token[]
     */
    public function getIterator(): Traversable
    {
        foreach ($this->source as $chunk) {
            foreach (mb_str_split($chunk) as $character) {
                $this->inString = $character == '"' && !$this->isEscape && !$this->inString;
                $this->isEscape = $character == '\\' && !$this->isEscape;

                if (isset(Tokens::BOUNDARIES[$character]) && $this->buffer != '' && !$this->inString) {
                    yield $this->toToken($this->buffer);
                    $this->buffer = '';

                    if (isset(Tokens::DELIMITERS[$character])) {
                        yield $this->toToken($character);
                    }
                } elseif (!$this->isEscape) {
                    $this->buffer .= $character;
                }
            }
        }

        if ($this->buffer != '') {
            // @todo test whether this is ever called
            yield $this->toToken($this->buffer);
            $this->buffer = '';
        }
    }

    /**
     * Turn the given value into a token
     *
     * @param string $value
     * @return Token
     */
    protected function toToken(string $value): Token
    {
        $character = $value[0];
        $type = Tokens::TYPES[$character];

        return static::$tokensMap[$type]->setValue($value);
    }
}
