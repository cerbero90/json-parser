<?php

namespace Cerbero\JsonParser\Tokens;

/**
 * The tokenizer.
 *
 */
final class Tokenizer
{
    /**
     * The singleton instance.
     *
     * @var static
     */
    private static self $instance;

    /**
     * The map of token instances by type.
     *
     * @var array<int, Token>
     */
    private array $tokensMap;

    /**
     * Instantiate the class.
     *
     */
    private function __construct()
    {
        $this->setTokensMap();
    }

    /**
     * Retrieve the singleton instance
     *
     * @return static
     */
    public static function instance(): static
    {
        return static::$instance ??= new static();
    }

    /**
     * Set the tokens map
     *
     * @return void
     */
    private function setTokensMap(): void
    {
        $instances = [];

        foreach (Tokens::MAP as $type => $class) {
            $this->tokensMap[$type] = $instances[$class] ??= new $class();
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

        return $this->tokensMap[$type]->setValue($value);
    }
}
