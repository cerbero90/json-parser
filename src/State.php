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
     * @param Pointer ...$pointers
     */
    public function __construct(Pointer ...$pointers)
    {
        $this->pointers = new Pointers(...$pointers);
        $this->tree = new Tree($this->pointers);
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
        return $this->pointers->matching() == ''
            ? $this->tree->depth() > $this->pointers->matching()->depth()
            : $this->tree->depth() >= $this->pointers->matching()->depth();
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
     * Determine whether the parser can stop parsing
     *
     * @return bool
     */
    public function canStopParsing(): bool
    {
        return $this->pointers->wereFound() && !$this->pointers->matching()->includesTree($this->tree);
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
        return $this->pointers->matching()->call($value, $key);
    }

    /**
     * Mutate state depending on the given token
     *
     * @param Token $token
     * @return void
     */
    public function mutateByToken(Token $token): void
    {
        $pointer = $this->pointers->matching();
        $shouldTrackTree = $pointer == '' || $this->tree->depth() < $pointer->depth();

        if ($shouldTrackTree && $this->expectsKey) {
            $this->tree->traverseKey($token);
        } elseif ($shouldTrackTree && $token->isValue() && !$this->tree->inObject()) {
            $this->tree->traverseArray();
        }

        $shouldBuffer = $this->tree->depth() >= 0
            && $this->pointers->matching()->matchesTree($this->tree)
            && ((!$this->expectsKey && $token->isValue()) || $this->treeIsDeep());

        if ($shouldBuffer) {
            $this->buffer .= $token;
            $this->pointers->markAsFound();
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
