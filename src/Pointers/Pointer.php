<?php

namespace Cerbero\JsonParser\Pointers;

use Stringable;

/**
 * The JSON pointer.
 *
 */
class Pointer implements Stringable
{
    /**
     * Instantiate the class.
     *
     * @param string $pointer
     */
    public function __construct(protected string $pointer)
    {
    }

    /**
     * Retrieve the underlying JSON pointer
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->pointer;
    }
}
