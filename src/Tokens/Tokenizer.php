<?php

namespace Cerbero\JsonParser\Tokens;

/**
 * The tokenizer.
 *
 */
class Tokenizer
{
    /**
     * The map of token instances by type.
     *
     * @var array<int, Token>
     */
    protected static array $tokensMap;

    /**
     * Instantiate the class.
     *
     */
    public function __construct()
    {
        static::$tokensMap ??= $this->hydrateTokensMap();
    }

    /**
     * Retrieve the hydrated tokens map
     *
     * @return array<int, Token>
     */
    protected function hydrateTokensMap(): array
    {
        $map = $instances = [];

        foreach (Tokens::MAP as $type => $class) {
            $map[$type] = $instances[$class] ??= new $class();
        }

        return $map;
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
