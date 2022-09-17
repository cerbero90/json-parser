<?php

namespace Cerbero\JsonParser\Sources;

use IteratorAggregate;
use Traversable;

/**
 * The JSON source.
 *
 */
class Source implements IteratorAggregate
{
    /**
     * Instantiate the class.
     *
     * @param mixed $source
     */
    public function __construct(protected mixed $source)
    {
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        //
    }
}
