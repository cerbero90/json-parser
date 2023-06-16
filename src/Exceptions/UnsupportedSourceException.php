<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * The exception thrown when a JSON source is not supported.
 *
 */
final class UnsupportedSourceException extends Exception implements JsonParserException
{
    /**
     * Instantiate the class.
     *
     * @param mixed $source
     */
    public function __construct(public readonly mixed $source)
    {
        parent::__construct('Unable to load JSON from the provided source');
    }
}
