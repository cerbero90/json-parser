<?php

namespace Cerbero\JsonParser\Pointers;

use Cerbero\JsonParser\Exceptions\PointerException;
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
     * Whether the pointer was found.
     *
     * @var bool
     */
    public bool $wasFound = false;

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
     * @return string[]
     */
    protected function toReferenceTokens(): array
    {
        if (preg_match('#^(?:/(?:(?:[^/~])|(?:~[01]))*)*$#', $this->pointer) === 0) {
            throw PointerException::invalid($this->pointer);
        }

        $tokens = explode('/', $this->pointer);
        $referenceTokens = array_map(fn (string $token) => str_replace(['~1', '~0'], ['/', '~'], $token), $tokens);

        return array_slice($referenceTokens, 1);
    }

    /**
     * Retrieve the reference tokens
     *
     * @return string[]
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
     * Determine whether the pointer includes the given tree
     *
     * @param Tree $tree
     * @return bool
     */
    public function includesTree(Tree $tree): bool
    {
        if ($this->pointer == '') {
            return true;
        }

        return (($firstNest = array_search('-', $this->referenceTokens)) !== false)
            && array_slice($this->referenceTokens, 0, $firstNest) == array_slice($tree->original(), 0, $firstNest);
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
