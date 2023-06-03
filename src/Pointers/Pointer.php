<?php

namespace Cerbero\JsonParser\Pointers;

use Cerbero\JsonParser\Exceptions\InvalidPointerException;
use Cerbero\JsonParser\ValueObjects\Tree;
use Closure;
use Stringable;

use function count;
use function is_int;
use function array_slice;

/**
 * The JSON pointer.
 *
 */
final class Pointer implements Stringable
{
    /**
     * The reference tokens.
     *
     * @var string[]
     */
    private array $referenceTokens;

    /**
     * The pointer depth.
     *
     * @var int
     */
    private int $depth;

    /**
     * The pointer callback.
     *
     * @var Closure
     */
    private ?Closure $callback;

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
     * @param bool $isLazy
     * @param Closure|null $callback
     */
    public function __construct(private string $pointer, private bool $isLazy = false, Closure $callback = null)
    {
        $this->referenceTokens = $this->toReferenceTokens();
        $this->depth = count($this->referenceTokens);
        $this->callback = $callback;
    }

    /**
     * Turn the JSON pointer into reference tokens
     *
     * @return string[]
     */
    private function toReferenceTokens(): array
    {
        if (preg_match('#^(?:/(?:(?:[^/~])|(?:~[01]))*)*$#', $this->pointer) === 0) {
            throw new InvalidPointerException($this->pointer);
        }

        $tokens = explode('/', $this->pointer);
        $referenceTokens = array_map(fn (string $token) => str_replace(['~1', '~0'], ['/', '~'], $token), $tokens);

        return array_slice($referenceTokens, 1);
    }

    /**
     * Determine whether the pointer is lazy
     *
     * @return bool
     */
    public function isLazy(): bool
    {
        return $this->isLazy;
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
     * Call the pointer callback
     *
     * @param mixed $value
     * @param mixed $key
     * @return mixed
     */
    public function call(mixed $value, mixed &$key): mixed
    {
        if ($this->callback === null) {
            return $value;
        }

        return ($this->callback)($value, $key) ?? $value;
    }

    /**
     * Determine whether the reference token at the given depth matches the provided key
     *
     * @param int $depth
     * @param string|int $key
     * @return bool
     */
    public function depthMatchesKey(int $depth, string|int $key): bool
    {
        $referenceToken = $this->referenceTokens[$depth] ?? null;

        return $referenceToken === (string) $key
            || (is_int($key) && $referenceToken === '-');
    }

    /**
     * Determine whether the pointer matches the given tree
     *
     * @param Tree $tree
     * @return bool
     */
    public function matchesTree(Tree $tree): bool
    {
        return $this->referenceTokens == []
            || $this->referenceTokens == $tree->original()
            || $this->referenceTokens == $tree->wildcarded();
    }

    /**
     * Determine whether the pointer includes the given tree
     *
     * @param Tree $tree
     * @return bool
     */
    public function includesTree(Tree $tree): bool
    {
        $originalTree = $tree->original();

        if ($this->pointer == '' || end($this->referenceTokens) == end($originalTree)) {
            return true;
        }

        return is_int($firstNest = array_search('-', $this->referenceTokens))
            && array_slice($this->referenceTokens, 0, $firstNest) === array_slice($originalTree, 0, $firstNest);
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
