<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * The syntax exception.
 *
 */
final class SyntaxException extends Exception implements JsonParserException
{
    /**
     * Instantiate the class
     *
     * @param string $value
     * @param int $position
     */
    public function __construct(public string $value, public int $position)
    {
        parent::__construct("Syntax error: unexpected [$value] at position {$position}");
    }
}
