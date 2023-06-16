<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * The exception thrown when Guzzle is not installed.
 *
 */
final class GuzzleRequiredException extends Exception implements JsonParserException
{
    /**
     * Instantiate the class.
     *
     */
    public function __construct()
    {
        parent::__construct('Guzzle is required to load JSON from endpoints');
    }
}
