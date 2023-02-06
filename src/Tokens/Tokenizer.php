<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\Exceptions\ParserException;
use Cerbero\JsonParser\Exceptions\SyntaxException;

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
     * Retrieve the singleton instance
     *
     * @return static
     */
    public static function instance(): static
    {
        return static::$instance ??= new static();
    }

    /**
     * Instantiate the class.
     *
     */
    private function __construct()
    {
        $this->setTokensMap();
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
     * @param int $position
     * @return Token
     */
    public function toToken(string $value, int $position): Token
    {
        $character = $value[0];

        if (!isset(Tokens::TYPES[$character])) {
            throw new SyntaxException($value, $position);
        }

        $type = Tokens::TYPES[$character];

        return $this->tokensMap[$type]->setValue($value);
    }
}
