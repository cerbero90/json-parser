<?php

namespace Cerbero\JsonParser\Exceptions;

use Cerbero\JsonParser\Pointers\Pointer;
use Exception;

/**
 * The exception thrown when two JSON pointers intersect.
 *
 */
class IntersectingPointersException extends Exception implements JsonParserException
{
    /**
     * Instantiate the class.
     *
     * @param Pointer $pointer1
     * @param Pointer $pointer2
     */
    public function __construct(public readonly Pointer $pointer1, public readonly Pointer $pointer2)
    {
        parent::__construct("The pointers [$pointer1] and [$pointer2] are intersecting");
    }
}
