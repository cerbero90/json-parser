<?php

namespace Cerbero\JsonParser;

use function count;

/**
 * The JSON tree.
 *
 */
final class Tree
{
    /**
     * The original JSON tree.
     *
     * @var array<int, string|int>
     */
    private array $original = [];

    /**
     * The wildcarded JSON tree.
     *
     * @var array<int, string|int>
     */
    private array $wildcarded = [];

    /**
     * Whether a depth is within an object.
     *
     * @var array<int, bool>
     */
    private array $inObjectByDepth = [];

    /**
     * The JSON tree depth.
     *
     * @var int
     */
    private int $depth = -1;

    /**
     * Whether the tree changed.
     *
     * @var bool
     */
    public bool $changed = false;

    /**
     * Retrieve the original JSON tree
     *
     * @return array<int, string|int>
     */
    public function original(): array
    {
        return $this->original;
    }

    /**
     * Retrieve the wildcarded JSON tree
     *
     * @return array<int, string|int>
     */
    public function wildcarded(): array
    {
        return $this->wildcarded;
    }

    /**
     * Determine whether the current depth is within an object
     *
     * @return bool
     */
    public function inObject(): bool
    {
        return $this->inObjectByDepth[$this->depth] ?? false;
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
     * Increase the tree depth by entering an object or an array
     *
     * @param bool $inObject
     * @return void
     */
    public function deepen(bool $inObject): void
    {
        $this->depth++;
        $this->inObjectByDepth[$this->depth] = $inObject;
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
     * Traverse the given object key
     *
     * @param string $key
     * @return void
     */
    public function traverseKey(string $key): void
    {
        $trimmedKey = substr($key, 1, -1);

        $this->original[$this->depth] = $trimmedKey;
        $this->wildcarded[$this->depth] = $trimmedKey;
        $this->changed = true;
    }

    /**
     * Traverse an array
     *
     * @param string[] $referenceTokens
     * @return void
     */
    public function traverseArray(array $referenceTokens): void
    {
        $referenceToken = $referenceTokens[$this->depth] ?? null;
        $index = $this->original[$this->depth] ?? null;

        $this->original[$this->depth] = is_int($index) ? $index + 1 : 0;
        $this->wildcarded[$this->depth] = $referenceToken == '-' ? '-' : $this->original[$this->depth];
        $this->changed = true;

        if (count($this->original) > $this->depth + 1) {
            array_splice($this->original, $this->depth + 1);
            array_splice($this->wildcarded, $this->depth + 1);
        }
    }

    /**
     * Retrieve the current key
     *
     * @return string|int
     */
    public function currentKey(): string|int
    {
        $key = $this->original[$this->depth];

        return is_string($key) ? "\"$key\"" : $key;
    }
}
