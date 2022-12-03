<?php

namespace Cerbero\JsonParser\Exceptions;

/**
 * The exception thrown when a source-related error occurs.
 *
 */
class SourceException extends JsonParserException
{
    /**
     * Retrieve the exception when a JSON source is not supported
     *
     * @return static
     */
    public static function unsupported(): static
    {
        return new static('Unable to load JSON from the provided source', static::SOURCE_UNSUPPORTED);
    }

    /**
     * Retrieve the exception when Guzzle is required
     *
     * @return static
     */
    public static function requireGuzzle(): static
    {
        return new static('Guzzle is required to load JSON from endpoints', static::SOURCE_GUZZLE);
    }
}
