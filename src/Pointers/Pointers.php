<?php

namespace Cerbero\JsonParser\Pointers;

use Cerbero\JsonParser\Tree;

/**
 * The JSON pointers collection.
 *
 */
final class Pointers
{
    /**
     * The JSON pointers collection.
     *
     * @var Pointer[]
     */
    private array $pointers;

    /**
     * The list of pointers that were found within the JSON.
     *
     * @var array<string, bool>
     */
    private array $found = [];

    /**
     * Instantiate the class.
     *
     * @param Pointer ...$pointers
     */
    public function __construct(Pointer ...$pointers)
    {
        $this->pointers = $pointers;
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
            foreach ($tree->original() as $depth => $key) {
                if (!$pointer->depthMatchesKey($depth, $key)) {
                    continue 2;
                } elseif (!isset($pointers[$depth])) {
                    $pointers[$depth] = $pointer;
                }
            }
        }

        return end($pointers) ?: $this->pointers[0];
    }

    /**
     * Mark the given pointer as found
     *
     * @param Pointer $pointer
     * @return void
     */
    public function markAsFound(Pointer $pointer): void
    {
        if (!$pointer->wasFound) {
            $this->found[(string) $pointer] = $pointer->wasFound = true;
        }
    }

    /**
     * Determine whether all pointers were found within the JSON
     *
     * @return bool
     */
    public function wereFound(): bool
    {
        return $this->count() == count($this->found);
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
}
