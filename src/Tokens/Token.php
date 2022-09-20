<?php

namespace Cerbero\JsonParser\Tokens;

/**
 * The abstract implementation of a token.
 *
 */
abstract class Token
{
    /**
     * Instantiate the class.
     *
     * @param mixed $value
     */
    public function __construct(protected mixed $value)
    {
    }

    /**
     * Retrieve the underlying value
     *
     * @return mixed
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * Determine whether the JSON parsing should continue after this token
     *
     * @return bool
     */
    public function shouldContinue(): bool
    {
        return false;
    }
}
