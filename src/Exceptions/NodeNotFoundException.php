<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * The exception thrown when trying to access a not existing node.
 *
 */
class NodeNotFoundException extends Exception implements JsonParserException
{
    /**
     * Instantiate the class.
     *
     * @param mixed $node
     */
    public function __construct(public mixed $node)
    {
        parent::__construct("The node [$node] was not found");
    }
}
