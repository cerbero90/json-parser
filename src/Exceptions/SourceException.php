<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * The exception thrown when a source-related error occurs.
 *
 */
final class SourceException extends Exception implements JsonParserException
{
    public const CODE_UNSUPPORTED = 0;
    public const CODE_GUZZLE = 1;

    /**
     * Retrieve the exception when a JSON source is not supported
     *
     * @return static
     */
    public static function unsupported(): static
    {
        return new static('Unable to load JSON from the provided source', static::CODE_UNSUPPORTED);
    }

    /**
     * Retrieve the exception when Guzzle is required
     *
     * @return static
     */
    public static function requireGuzzle(): static
    {
        return new static('Guzzle is required to load JSON from endpoints', static::CODE_GUZZLE);
    }
}
