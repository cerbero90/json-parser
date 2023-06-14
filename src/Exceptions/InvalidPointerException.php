<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * The exception thrown when a JSON pointer syntax is not valid.
 *
 */
final class InvalidPointerException extends Exception implements JsonParserException
{
    /**
     * Instantiate the class.
     *
     * @param string $pointer
     */
    public function __construct(public readonly string $pointer)
    {
        parent::__construct("The string [$pointer] is not a valid JSON pointer");
    }
}
