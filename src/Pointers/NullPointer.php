<?php

namespace Cerbero\JsonParser\Pointers;

use Cerbero\JsonParser\Tree;

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

    /**
     * Determine whether the pointer matches the given tree
     *
     * @param Tree $tree
     * @return bool
     */
    public function matchesTree(Tree $tree): bool
    {
        return true;
    }
}
