<?php

namespace Cerbero\JsonParser\Exceptions;

/**
 * The exception thrown when a pointer-related error occurs.
 *
 */
class PointerException extends JsonParserException
{
    /**
     * Retrieve the exception when the given pointer is invalid
     *
     * @param string $pointer
     * @return static
     */
    public static function invalid(string $pointer): static
    {
        return new static("The string [$pointer] is not a valid JSON pointer", static::POINTER_INVALID);
    }
}
