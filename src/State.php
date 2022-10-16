<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Pointers\Pointer;
use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Tokens\Scalar;
use Cerbero\JsonParser\Tokens\Token;
use Cerbero\JsonParser\Tokens\Value;

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
     * Whether the token should be an object key.
     *
     * @var bool
     */
    protected bool $expectsKey = false;

    /**
     * Whether the currently parsed node is an object.
     *
     * @var bool
     */
    protected bool $inObject = false;

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
        return $this->tree->depth() < $this->pointer->depth();
    }

    /**
     * Determine whether the tree is deep
     *
     * @return bool
     */
    public function treeIsDeep(): bool
    {
        return $this->tree->depth() > $this->pointer->depth();
    }

    /**
     * Retrieve the current node of the JSON tree
     *
     * @return string
     */
    public function node(): string
    {
        return $this->tree[$this->tree->depth()];
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
    public function treeDidntChange(): static
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
    public function treeInPointer(): bool
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
        return $this->pointer->matchesTree($this->tree);
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
        return $this->pointer->matchesTree($this->tree)
            && ($this->treeIsDeep() || (!$this->expectsKey && $this->expectsToken($token)));
    }

    /**
     * Determine whether the given token is expected
     *
     * @param Token $token
     * @return bool
     */
    protected function expectsToken(Token $token): bool
    {
        return ($this->tree->depth() == $this->pointer->depth() && $token instanceof Value)
            || ($this->tree->depth() + 1 == $this->pointer->depth() && $token instanceof Scalar);
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
     * Determine whether the currently parsed node is an object
     *
     * @return bool
     */
    public function inObject(): bool
    {
        return $this->inObject;
    }
}
