<?php

namespace Cerbero\JsonParser\Pointers;

use Stringable;

/**
 * The JSON pointers collection.
 *
 */
class Pointers
{
    /**
     * The JSON pointers collection.
     *
     * @var Pointer[]
     */
    protected array $pointers;

    /**
     * Instantiate the class.
     *
     * @param Pointer ...$pointers
     */
    public function __construct(Pointer ...$pointers)
    {
        $this->pointers = $pointers;
    }
}
