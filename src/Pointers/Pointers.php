<?php

namespace Cerbero\JsonParser\Pointers;

use Cerbero\JsonParser\Tree;
use Countable;

/**
 * The JSON pointers collection.
 *
 */
class Pointers implements Countable
{
    /**
     * The JSON pointers collection.
     *
     * @var Pointer[]
     */
    protected array $pointers;

    /**
     * The default pointer.
     *
     * @var Pointer
     */
    protected Pointer $defaultPointer;

    /**
     * The list of pointers that were found within the JSON.
     *
     * @var array
     */
    protected array $found = [];

    /**
     * Instantiate the class.
     *
     * @param Pointer ...$pointers
     */
    public function __construct(Pointer ...$pointers)
    {
        $this->pointers = $pointers;
        $this->defaultPointer = new NullPointer();
    }

    /**
     * Retrieve the number of JSON pointers
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->pointers);
    }

    /**
     * Retrieve the pointer matching the given tree
     *
     * @param Tree $tree
     * @return Pointer
     */
    public function matchTree(Tree $tree): Pointer
    {
        $pointers = [];

        foreach ($this->pointers as $pointer) {
            foreach ($tree as $depth => $node) {
                if (!$pointer->depthMatchesNode($depth, $node)) {
                    continue 2;
                } elseif (!isset($pointers[$depth])) {
                    $pointers[$depth] = $pointer;
                }
            }
        }

        return end($pointers) ?: $this->defaultPointer;
    }

    /**
     * Determine whether all pointers were found within the JSON
     *
     * @return bool
     */
    public function wereFound(): bool
    {
        return count($this->pointers) == count($this->found);
    }
}
