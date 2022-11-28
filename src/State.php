<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Pointers\Pointer;
use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Tokens\Token;

/**
 * The JSON parsing state.
 *
 */
class State
{
    /**
     * The JSON tree.
     *
     * @var Tree
     */
    protected Tree $tree;

    /**
     * Whether the tree changed.
     *
     * @var bool
     */
    protected bool $treeChanged = false;

    /**
     * The JSON pointer matching the tree.
     *
     * @var Pointer
     */
    protected Pointer $pointer;

    /**
     * The JSON buffer.
     *
     * @var string
     */
    protected string $buffer = '';

    /**
     * Whether an object key is expected.
     *
     * @var bool
     */
    protected bool $expectsKey = false;

    /**
     * Instantiate the class.
     *
     */
    public function __construct()
    {
        $this->tree = new Tree();
    }

    /**
     * Retrieve the JSON tree
     *
     * @return Tree
     */
    public function tree(): Tree
    {
        return $this->tree;
    }

    /**
     * Determine whether the tree is shallow
     *
     * @return bool
     */
    public function treeIsShallow(): bool
    {
        return $this->pointer == ''
            || $this->tree->depth() < $this->pointer->depth();
    }

    /**
     * Determine whether the tree is deep
     *
     * @return bool
     */
    public function treeIsDeep(): bool
    {
        return $this->pointer == ''
            ? $this->tree->depth() > $this->pointer->depth()
            : $this->tree->depth() >= $this->pointer->depth();
    }

    /**
     * Retrieve the current key of the JSON tree
     *
     * @return string
     */
    public function key(): string
    {
        return $this->tree->currentKey();
    }

    /**
     * Determine whether the tree changed
     *
     * @return bool
     */
    public function treeChanged(): bool
    {
        return $this->treeChanged;
    }

    /**
     * Mark the JSON tree as not changed
     *
     * @return static
     */
    public function treeDidNotChange(): static
    {
        $this->treeChanged = false;

        return $this;
    }

    /**
     * Set the JSON pointer matching the tree from the given pointers
     *
     * @param Pointers $pointers
     * @return static
     */
    public function matchPointer(Pointers $pointers): static
    {
        $this->pointer = $pointers->matchTree($this->tree);

        return $this;
    }

    /**
     * Retrieve the JSON pointer matching the tree
     *
     * @return Pointer
     */
    public function pointer(): Pointer
    {
        return $this->pointer;
    }

    /**
     * Determine whether the tree is within the JSON pointer
     *
     * @return bool
     */
    public function inPointer(): bool
    {
        return $this->pointer->includesTree($this->tree);
    }

    /**
     * Determine whether the tree matches the JSON pointer
     *
     * @return bool
     */
    public function pointerMatchesTree(): bool
    {
        return $this->pointer == ''
            || in_array($this->pointer->referenceTokens(), [$this->tree->original(), $this->tree->wildcarded()]);
    }

    /**
     * Traverse the given object key
     *
     * @param string $key
     * @return static
     */
    public function traverseKey(string $key): static
    {
        $this->tree->traverseKey($key);
        $this->treeChanged = true;

        return $this;
    }

    /**
     * Traverse a JSON array
     *
     * @return void
     */
    public function traverseArray(): void
    {
        $this->tree->traverseArray($this->pointer);
        $this->treeChanged = true;
    }

    /**
     * Determine whether the buffer contains tokens
     *
     * @return bool
     */
    public function hasBuffer(): bool
    {
        return $this->buffer != '';
    }

    /**
     * Determine whether the given token should be buffered
     *
     * @param Token $token
     * @return bool
     */
    public function shouldBufferToken(Token $token): bool
    {
        return $this->pointerMatchesTree()
            && ($this->treeIsDeep() || (!$this->expectsKey() && ($token->isValue() || $this->expectsToken($token))));
    }

    /**
     * Determine whether the given token is expected
     *
     * @param Token $token
     * @return bool
     */
    protected function expectsToken(Token $token): bool
    {
        return ($this->tree->depth() == $this->pointer->depth() && $token->isValue())
            || ($this->tree->depth() + 1 == $this->pointer->depth() && $token->isScalar());
    }

    /**
     * Buffer the given token
     *
     * @param Token $token
     * @return static
     */
    public function bufferToken(Token $token): static
    {
        $this->buffer .= $token;

        return $this;
    }

    /**
     * Retrieve and reset the buffer
     *
     * @return string
     */
    public function pullBuffer(): string
    {
        $buffer = $this->buffer;

        $this->buffer = '';

        return $buffer;
    }

    /**
     * Determine whether an object key is expected
     *
     * @return bool
     */
    public function expectsKey(): bool
    {
        return $this->expectsKey;
    }

    /**
     * Expect an object key
     *
     * @return static
     */
    public function expectKey(): static
    {
        $this->expectsKey = true;

        return $this;
    }

    /**
     * Do not expect any object key
     *
     * @return static
     */
    public function doNotExpectKey(): static
    {
        $this->expectsKey = false;

        return $this;
    }

    /**
     * Determine whether the current position is within an object
     *
     * @return bool
     */
    public function inObject(): bool
    {
        return $this->tree->inObject();
    }

    /**
     * Determine whether the tree is within the JSON root
     *
     * @return bool
     */
    public function inRoot(): bool
    {
        return $this->tree->depth() >= 0;
    }
}
