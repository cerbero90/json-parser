<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Pointers\Pointer;
use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Tokens\Token;
use Cerbero\JsonParser\Tokens\Tokens;

/**
 * The JSON parsing state.
 *
 */
final class State
{
    /**
     * The JSON tree.
     *
     * @var Tree
     */
    private Tree $tree;

    /**
     * The JSON pointers.
     *
     * @var Pointers
     */
    private Pointers $pointers;

    /**
     * The JSON pointer matching the tree.
     *
     * @var Pointer
     */
    private Pointer $pointer;

    /**
     * The JSON buffer.
     *
     * @var string
     */
    private string $buffer = '';

    /**
     * Whether an object key is expected.
     *
     * @var bool
     */
    public bool $expectsKey = false;

    /**
     * The expected token.
     *
     * @var int
     */
    public int $expectedToken = Tokens::COMPOUND_BEGIN;

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
     * @return string|int
     */
    public function key(): string|int
    {
        return $this->tree->currentKey();
    }

    /**
     * Set and match the given pointers
     *
     * @param Pointer ...$pointers
     * @return void
     */
    public function setPointers(Pointer ...$pointers): void
    {
        $this->pointers = new Pointers(...$pointers ?: [new Pointer('')]);

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
     * Call the current pointer callback
     *
     * @param mixed $value
     * @param mixed $key
     * @return mixed
     */
    public function callPointer(mixed $value, mixed $key): mixed
    {
        return $this->pointer->call($value, $key);
    }

    /**
     * Mutate state depending on the given token
     *
     * @param Token $token
     * @return void
     */
    public function mutateByToken(Token $token): void
    {
        $this->tree->changed = false;
        $shouldTrackTree = $this->pointer == '' || $this->tree->depth() < $this->pointer->depth();

        if ($shouldTrackTree && $this->expectsKey) {
            $this->tree->traverseKey($token);
        } elseif ($shouldTrackTree && $token->isValue() && !$this->tree->inObject()) {
            $this->tree->traverseArray($this->pointer->referenceTokens());
        }

        if ($this->tree->changed && $this->pointers->count() > 1) {
            $this->pointer = $this->pointers->matchTree($this->tree);
        }

        $shouldBuffer = $this->tree->depth() >= 0
            && $this->pointer->matchesTree($this->tree)
            && ((!$this->expectsKey && $token->isValue()) || $this->treeIsDeep());

        if ($shouldBuffer) {
            $this->buffer .= $token;
            $this->pointers->markAsFound($this->pointer);
        }

        $token->mutateState($this);
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
