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
    public bool $expectsKey = false;

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
     * Determine whether the current position is within an object
     *
     * @return bool
     */
    public function inObject(): bool
    {
        $tree = $this->tree->original();
        $depth = $this->tree->depth();

        return is_string($tree[$depth] ?? null);
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

        $this->pointer = $this->pointers->matchTree($this->tree);
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
     * Mutate state depending on the given token
     *
     * @param Token $token
     * @return void
     */
    public function mutateByToken(Token $token): void
    {
        $treeChanged = false;
        $shouldTrackTree = $this->pointer == '' || $this->tree->depth() < $this->pointer->depth();

        if ($shouldTrackTree && $token->isValue() && !$this->inObject()) {
            $this->tree->traverseArray($this->pointer->referenceTokens());
            $treeChanged = true;
        }

        if ($shouldTrackTree && $this->expectsKey) {
            $this->tree->traverseKey($token);
            $treeChanged = true;
        }

        $this->bufferToken($token);

        if ($treeChanged && $this->pointers->count() > 1) {
            $this->pointer = $this->pointers->matchTree($this->tree);
        }

        $token->mutateState($this);
    }

    /**
     * Buffer the given token
     *
     * @param Token $token
     * @return void
     */
    protected function bufferToken(Token $token): void
    {
        $shouldBuffer = $this->tree->depth() >= 0
            && $this->pointerMatchesTree()
            && ($this->treeIsDeep() || ($token->isValue() && !$this->expectsKey));

        if ($shouldBuffer) {
            $this->buffer .= $token;
            $this->pointers->markAsFound($this->pointer);
        }
    }

    /**
     * Determine whether the tree matches the JSON pointer
     *
     * @return bool
     */
    protected function pointerMatchesTree(): bool
    {
        return in_array($this->pointer->referenceTokens(), [[], $this->tree->original(), $this->tree->wildcarded()]);
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
     * Retrieve the value from the buffer and reset it
     *
     * @return string
     */
    public function value(): string
    {
        $buffer = $this->buffer;

        $this->buffer = '';

        return $buffer;
    }
}
