<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * Any exception thrown by JSON Parser.
 *
 */
abstract class JsonParserException extends Exception
{
    public const SOURCE_UNSUPPORTED = 0;
    public const SOURCE_GUZZLE = 1;

    public const POINTER_INVALID = 0;
}
