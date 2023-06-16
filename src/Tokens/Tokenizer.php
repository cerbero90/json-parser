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
     * @var self
     */
    private static self $instance;

    /**
     * The map of token instances by type.
     *
     * @var array<int, Token>
     */
    private array $tokensMap = [];

    /**
     * Retrieve the singleton instance
     *
     * @return self
     */
    public static function instance(): self
    {
        return self::$instance ??= new self();
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
     * @return Token
     */
    public function toToken(string $value): Token
    {
        $type = Tokens::TYPES[$value[0]];

        return $this->tokensMap[$type]->setValue($value);
    }
}
