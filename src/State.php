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
     * The JSON pointers.
     *
     * @var Pointers
     */
    protected Pointers $pointers;

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
     * Determine whether the tree should be tracked
     *
     * @return bool
     */
    public function shouldTrackTree(): bool
    {
        return $this->pointer == '' || $this->tree->depth() < $this->pointer->depth();
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
     * Traverse the given object key
     *
     * @param string $key
     * @return void
     */
    public function traverseKey(string $key): void
    {
        $this->tree->traverseKey($key);

        $this->treeChanged = true;
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
     * @return void
     */
    public function treeDidNotChange(): void
    {
        $this->treeChanged = false;
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
     * Set and match the given pointers
     *
     * @param Pointer ...$pointers
     * @return void
     */
    public function setPointers(Pointer ...$pointers): void
    {
        $this->pointers = new Pointers(...$pointers);

        $this->matchPointer();
    }

    /**
     * Set the JSON pointer matching the tree
     *
     * @return void
     */
    public function matchPointer(): void
    {
        $this->pointer = $this->pointers->matchTree($this->tree);
    }

    /**
     * Set the new matching JSON pointer when the tree changes
     *
     * @return void
     */
    public function rematchPointer(): void
    {
        if ($this->treeChanged && $this->pointers->count() > 1) {
            $this->matchPointer();
        }
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
     * Determine whether the parser can stop parsing
     *
     * @return bool
     */
    public function canStopParsing(): bool
    {
        return $this->pointers->wereFound() && !$this->pointer->includesTree($this->tree);
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
     * Determine whether the buffer contains tokens
     *
     * @return bool
     */
    public function hasBuffer(): bool
    {
        return $this->buffer != '';
    }

    /**
     * Buffer the given token
     *
     * @param Token $token
     * @return void
     */
    public function bufferToken(Token $token): void
    {
        $shouldBuffer = $this->tree->depth() >= 0
            && $this->pointerMatchesTree()
            && ($this->treeIsDeep() || ($token->isValue() && !$this->expectsKey()));

        if ($shouldBuffer) {
            $this->buffer .= $token;
            $this->pointers->markAsFound($this->pointer);
        }
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
     * @return void
     */
    public function expectKey(): void
    {
        $this->expectsKey = true;
    }

    /**
     * Do not expect any object key
     *
     * @return void
     */
    public function doNotExpectKey(): void
    {
        $this->expectsKey = false;
    }
}
