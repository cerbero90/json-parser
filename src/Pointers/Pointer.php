<?php

namespace Cerbero\JsonParser\Pointers;

use Cerbero\JsonParser\Tree;
use Stringable;

/**
 * The JSON pointer.
 *
 */
class Pointer implements Stringable
{
    /**
     * The reference tokens.
     *
     * @var string[]
     */
    protected array $referenceTokens;

    /**
     * The pointer depth.
     *
     * @var int
     */
    protected int $depth;

    /**
     * Instantiate the class.
     *
     * @param string $pointer
     */
    public function __construct(protected string $pointer)
    {
        $this->referenceTokens = $this->toReferenceTokens();
        $this->depth = count($this->referenceTokens);
    }

    /**
     * Turn the JSON pointer into reference tokens
     *
     * @return array
     */
    protected function toReferenceTokens(): array
    {
        $tokens = explode('/', substr($this->pointer, 1));

        return array_map(fn (string $token) => str_replace(['~1', '~0'], ['/', '~'], $token), $tokens);
    }

    /**
     * Retrieve the reference tokens
     *
     * @return array
     */
    public function referenceTokens(): array
    {
        return $this->referenceTokens;
    }

    /**
     * Retrieve the JSON pointer depth
     *
     * @return int
     */
    public function depth(): int
    {
        return $this->depth;
    }

    /**
     * Determine whether the reference token at the given depth matches the provided node
     *
     * @param int $depth
     * @param mixed $node
     * @return bool
     */
    public function depthMatchesNode(int $depth, mixed $node): bool
    {
        if (!isset($this->referenceTokens[$depth])) {
            return false;
        }

        if ($this->referenceTokens[$depth] === (string) $node) {
            return true;
        }

        return is_int($node) && $this->referenceTokens[$depth] === '-';
    }

    /**
     * Determine whether the pointer matches the given tree
     *
     * @param Tree $tree
     * @return bool
     */
    public function matchesTree(Tree $tree): bool
    {
        return $this->referenceTokens == $tree->original() || $this->referenceTokens == $tree->wildcarded();
    }

    /**
     * Determine whether the pointer includes the given tree
     *
     * @param Tree $tree
     * @return bool
     */
    public function includesTree(Tree $tree): bool
    {
        if (($firstNest = array_search('-', $this->referenceTokens)) === false) {
            return false;
        }

        return array_slice($this->referenceTokens, 0, $firstNest) == array_slice($tree->original(), 0, $firstNest);
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
