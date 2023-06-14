<?php

namespace Cerbero\JsonParser\ValueObjects;

use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Tokens\Token;

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
     * Instantiate the class.
     *
     * @param Pointers $pointers
     */
    public function __construct(private readonly Pointers $pointers)
    {
    }

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
     * Determine whether the tree is deep
     *
     * @return bool
     */
    public function isDeep(): bool
    {
        $pointer = $this->pointers->matching();

        return $pointer == '' ? $this->depth > 0 : $this->depth >= $pointer->depth;
    }

    /**
     * Traverse the given token
     *
     * @param Token $token
     * @param bool $expectsKey
     * @return void
     */
    public function traverseToken(Token $token, bool $expectsKey): void
    {
        $pointer = $this->pointers->matching();

        if ($pointer != '' && $this->depth >= $pointer->depth) {
            return;
        } elseif ($expectsKey) {
            $this->traverseKey($token);
        } elseif ($token->isValue() && !$this->inObject()) {
            $this->traverseArray();
        }
    }

    /**
     * Determine whether the tree is matched by the JSON pointer
     *
     * @return bool
     */
    public function isMatched(): bool
    {
        return $this->depth >= 0 && $this->pointers->matching()->matchesTree($this);
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

        if (count($this->original) > $offset = $this->depth + 1) {
            array_splice($this->original, $offset);
            array_splice($this->wildcarded, $offset);
            array_splice($this->inObjectByDepth, $offset);
        }

        $this->pointers->matchTree($this);
    }

    /**
     * Traverse an array
     *
     * @return void
     */
    public function traverseArray(): void
    {
        $index = $this->original[$this->depth] ?? null;
        $this->original[$this->depth] = $index = is_int($index) ? $index + 1 : 0;

        if (count($this->original) > $offset = $this->depth + 1) {
            array_splice($this->original, $offset);
            array_splice($this->inObjectByDepth, $offset);
        }

        $referenceTokens = $this->pointers->matchTree($this)->referenceTokens;
        $this->wildcarded[$this->depth] = ($referenceTokens[$this->depth] ?? null) == '-' ? '-' : $index;

        if (count($this->wildcarded) > $offset) {
            array_splice($this->wildcarded, $offset);
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
