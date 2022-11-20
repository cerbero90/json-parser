<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * Any exception thrown by JSON Parser.
 *
 */
abstract class JsonParserException extends Exception
{
    public const CODE_SOURCE_INVALID = 0;
    public const CODE_SOURCE_UNSUPPORTED = 1;
    public const CODE_SOURCE_GUZZLE = 2;

    public const CODE_POINTER_INVALID = 3;
}
