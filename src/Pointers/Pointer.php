<?php

namespace Cerbero\JsonParser\Pointers;

use ArrayAccess;
use Cerbero\JsonParser\Tree;
use Stringable;

/**
 * The JSON pointer.
 *
 */
class Pointer implements ArrayAccess, Stringable
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
     * Determine whether the reference token at the given depth matches the provided key
     *
     * @param int $depth
     * @param mixed $key
     * @return bool
     */
    public function depthMatchesKey(int $depth, mixed $key): bool
    {
        if (!isset($this->referenceTokens[$depth])) {
            return false;
        }

        if ($this->referenceTokens[$depth] === (string) $key) {
            return true;
        }

        return is_int($key) && $this->referenceTokens[$depth] === '-';
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
     * Determine whether the given reference token exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->referenceTokens[$offset]);
    }

    /**
     * Retrieve the given reference token
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->referenceTokens[$offset] ?? null;
    }

    /**
     * Do not set any reference token
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        return;
    }

    /**
     * Do not unset any reference token
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        return;
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
