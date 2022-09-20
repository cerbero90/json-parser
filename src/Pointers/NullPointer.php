<?php

namespace Cerbero\JsonParser\Pointers;

/**
 * The null pointer.
 *
 */
class NullPointer extends Pointer
{
    /**
     * The reference tokens.
     *
     * @var string[]
     */
    protected array $referenceTokens = [];

    /**
     * The pointer depth.
     *
     * @var int
     */
    protected int $depth = 0;

    /**
     * Instantiate the class.
     *
     */
    public function __construct()
    {
        $this->pointer = '';
    }
}
