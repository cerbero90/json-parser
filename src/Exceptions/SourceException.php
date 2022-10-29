<?php

namespace Cerbero\JsonParser\Exceptions;

/**
 * The exception thrown when a source-related error occurs.
 *
 */
class SourceException extends JsonParserException
{
    /**
     * Retrieve the exception when the given source is invalid
     *
     * @param string $source
     * @return static
     */
    public static function invalidSource(string $source): static
    {
        return new static("[$source] is not a valid source");
    }

    /**
     * Retrieve the exception when a JSON source is not supported
     *
     * @return static
     */
    public static function unsupported(): static
    {
        return new static('Unable to load JSON from the provided source');
    }

    /**
     * Retrieve the exception when Guzzle is required
     *
     * @return static
     */
    public static function requireGuzzle(): static
    {
        return new static('Guzzle is required to load JSON from endpoints');
    }
}