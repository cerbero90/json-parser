<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * Any exception thrown by JSON Parser.
 *
 */
abstract class JsonParserException extends Exception
{
    /**
     * Enforce factory methods to instantiate exceptions
     *
     * @param string $message
     */
    protected function __construct(string $message)
    {
        parent::__construct($message);
    }
}
