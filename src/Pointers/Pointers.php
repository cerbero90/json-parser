<?php

namespace Cerbero\JsonParser\Pointers;

use Cerbero\JsonParser\Exceptions\IntersectingPointersException;
use Cerbero\JsonParser\ValueObjects\Tree;

use function count;

/**
 * The JSON pointers aggregate.
 *
 */
final class Pointers
{
    /**
     * The JSON pointers.
     *
     * @var Pointer[]
     */
    private array $pointers = [];

    /**
     * The JSON pointer matching with the current tree.
     *
     * @var Pointer
     */
    private Pointer $matching;

    /**
     * The list of pointers that were found within the JSON.
     *
     * @var array<string, bool>
     */
    private array $found = [];

    /**
     * Add the given pointer
     *
     * @param Pointer $pointer
     */
    public function add(Pointer $pointer): void
    {
        foreach ($this->pointers as $existingPointer) {
            if (str_starts_with($existingPointer, "$pointer/") || str_starts_with($pointer, "$existingPointer/")) {
                throw new IntersectingPointersException($existingPointer, $pointer);
            }
        }

        $this->pointers[] = $pointer;
    }

    /**
     * Retrieve the pointer matching the current tree
     *
     * @return Pointer
     */
    public function matching(): Pointer
    {
        return $this->matching ??= $this->pointers[0] ?? new Pointer('');
    }

    /**
     * Retrieve the pointer matching the given tree
     *
     * @param Tree $tree
     * @return Pointer
     */
    public function matchTree(Tree $tree): Pointer
    {
        if (count($this->pointers) < 2) {
            return $this->matching;
        }

        $pointers = [];
        $originalTree = $tree->original();

        foreach ($this->pointers as $pointer) {
            if ($pointer->referenceTokens == $originalTree) {
                return $this->matching = $pointer;
            }

            foreach ($originalTree as $depth => $key) {
                if (!$pointer->depthMatchesKey($depth, $key)) {
                    continue 2;
                } elseif (!isset($pointers[$depth])) {
                    $pointers[$depth] = $pointer;
                }
            }
        }

        return $this->matching = end($pointers) ?: $this->matching;
    }

    /**
     * Mark the given pointer as found
     *
     * @return Pointer
     */
    public function markAsFound(): Pointer
    {
        if (!$this->matching->wasFound) {
            $this->found[(string) $this->matching] = $this->matching->wasFound = true;
        }

        return $this->matching;
    }

    /**
     * Determine whether all pointers were found in the given tree
     *
     * @param Tree $tree
     * @return bool
     */
    public function wereFoundInTree(Tree $tree): bool
    {
        return count($this->pointers) == count($this->found)
            && !empty($this->pointers)
            && !$this->matching->includesTree($tree);
    }
}
