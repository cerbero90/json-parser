<?php

namespace Cerbero\JsonParser;

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
     * @param Pointers $pointers
     */
    public function __construct(private Pointers $pointers)
    {
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
        return $this->pointers->wereFoundInTree($this->tree);
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
        $shouldTrackTree = $this->tree->shouldBeTracked();

        if ($shouldTrackTree && $this->expectsKey) {
            $this->tree->traverseKey($token);
        } elseif ($shouldTrackTree && $token->isValue() && !$this->tree->inObject()) {
            $this->tree->traverseArray();
        }

        if ($this->tree->isMatched() && ((!$this->expectsKey && $token->isValue()) || $this->tree->isDeep())) {
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
