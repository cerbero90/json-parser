<?php

namespace Cerbero\JsonParser\Tokens;

/**
 * The tokenizer.
 *
 */
class Tokenizer
{
    /**
     * The map of token instances.
     *
     * @var array<int, Token>
     */
    protected static array $tokensMap = [];

    /**
     * Instantiate the class.
     *
     */
    public function __construct()
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
     * Turn the given value into a token
     *
     * @param string $value
     * @return Token
     */
    public function toToken(string $value): Token
    {
        $character = $value[0];
        $type = Tokens::TYPES[$character];

        return static::$tokensMap[$type]->setValue($value);
    }
}
