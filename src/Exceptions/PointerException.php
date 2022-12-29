<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * The exception thrown when a pointer-related error occurs.
 *
 */
final class PointerException extends Exception implements JsonParserException
{
    public const CODE_INVALID = 0;

    /**
     * Retrieve the exception when the given pointer is invalid
     *
     * @param string $pointer
     * @return static
     */
    public static function invalid(string $pointer): static
    {
        return new static("The string [$pointer] is not a valid JSON pointer", static::CODE_INVALID);
    }
}
