<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Pointers\Pointer;
use IteratorAggregate;
use Traversable;

/**
 * The JSON tree.
 *
 */
class Tree implements IteratorAggregate
{
    /**
     * The original JSON tree.
     *
     * @var array
     */
    protected array $original = [];

    /**
     * The wildcarded JSON tree.
     *
     * @var array
     */
    protected array $wildcarded = [];

    /**
     * The JSON tree depth.
     *
     * @var int
     */
    protected int $depth = -1;

    /**
     * Retrieve the original JSON tree
     *
     * @return array
     */
    public function original(): array
    {
        return $this->original;
    }

    /**
     * Retrieve the wildcarded JSON tree
     *
     * @return array
     */
    public function wildcarded(): array
    {
        return $this->wildcarded;
    }

    /**
     * Retrieve the JSON tree depth
     *
     * @return int
     */
    public function depth(): int
    {
        return $this->depth;
    }

    /**
     * Increase the tree depth
     *
     * @return void
     */
    public function deepen(): void
    {
        $this->depth++;
    }

    /**
     * Decrease the tree depth
     *
     * @return void
     */
    public function emerge(): void
    {
        $this->depth--;
    }

    /**
     * Determine whether the tree is traversing an object
     *
     * @return bool
     */
    public function inObject(): bool
    {
        $key = $this->original[$this->depth] ?? null;

        return is_string($key);
    }

    /**
     * Traverse the given key
     *
     * @param string $key
     * @return void
     */
    public function traverse(string $key): void
    {
        $this->original[$this->depth] = $key;
        $this->wildcarded[$this->depth] = $key;

        $this->trim();
    }

    /**
     * Trim the tree after the latest traversed key
     *
     * @return void
     */
    protected function trim(): void
    {
        array_splice($this->original, $this->depth + 1);
        array_splice($this->wildcarded, $this->depth + 1);
    }

    /**
     * Traverse an array
     *
     * @param Pointer $pointer
     * @return void
     */
    public function traverseArray(Pointer $pointer): void
    {
        $this->original[$this->depth] = isset($this->original[$this->depth]) ? $this->original[$this->depth] + 1 : 0;
        $this->wildcarded[$this->depth] = $pointer[$this->depth] == '-' ? '-' : $this->original[$this->depth];

        $this->trim();
    }

    /**
     * Retrieve the original tree iterator
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        yield from $this->original();
    }
}
