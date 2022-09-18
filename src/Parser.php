<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Pointers\Pointer;
use Cerbero\JsonParser\Pointers\Pointers;
use Generator;
use IteratorAggregate;
use Traversable;

/**
 * The JSON parser.
 *
 */
class Parser implements IteratorAggregate
{
    /**
     * The JSON parsing state.
     *
     * @var State
     */
    protected State $state;

    /**
     * The JSON pointers collection.
     *
     * @var Pointers
     */
    protected Pointers $pointers;

    /**
     * The JSON pointer matching the current tree.
     *
     * @var Pointer
     */
    protected Pointer $pointer;

    /**
     * Instantiate the class.
     *
     * @param Lexer $lexer
     * @param Config $config
     */
    public function __construct(protected Lexer $lexer, protected Config $config)
    {
        $this->state = new State();
        $this->pointers = new Pointers(...$config->pointers);
        $this->pointer = $this->pointers->matchTree($this->state->tree);
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        foreach ($this->lexer as $token) {
            $this->rematchPointer();
            $this->traverseToken($token);
            $this->bufferToken($token);
            $this->mutateState($token);

            if ($this->state->depth > $this->pointer->depth()) {
                continue;
            }

            if ($this->state->buffer != '') {
                yield from $this->yieldDecodedBuffer();
            }

            if ($this->pointers->wereFound() && !$this->pointer->includesTree($this->tree)) {
                break;
            }
        }
    }

    /**
     * Set the matching JSON pointer when the tree changes
     *
     * @return void
     */
    protected function rematchPointer(): void
    {
        if ($this->state->treeChanged && $this->pointers->count() > 1) {
            $this->pointer = $this->pointers->matchTree($this->state->tree);
            $this->state->treeChanged = false;
        }
    }

    /**
     * Keep track of the JSON tree when traversing the given token
     *
     * @param Token $token
     * @return void
     */
    protected function traverseToken(Token $token): void
    {
        if (!$this->state->inObject && $token instanceof Value && $this->state->depth < $this->pointer->depth()) {
            $this->state->treeChanged = true;
            $this->state->tree->traverse($token);
        }
    }

    /**
     * Preserve the given token in the buffer
     *
     * @param Token $token
     * @return void
     */
    protected function bufferToken(Token $token): void
    {
        if ($this->pointer->matchesTree($this->state->tree) && $this->shouldBufferToken($token)) {
            $this->state->buffer .= $token;
        }
    }

    /**
     * Preserve the given token in the buffer
     *
     * @param Token $token
     * @return void
     */
    protected function mutateState(Token $token): void
    {
        if ($token instanceof StateMutator) {
            $token->mutateState($this->state);
        }
    }

    /**
     * Yield the decoded JSON of the buffer
     *
     * @return Generator
     */
    protected function yieldDecodedBuffer(): Generator
    {
        $decoded = $this->config->decoder->decode($this->state->buffer);
        $this->state->buffer = '';

        if (!$decoded->succeeded) {
            call_user_func($this->config->onError, $decoded);
        }

        if ($this->state->inObject) {
            yield $this->state->tree[$this->state->depth] => $decoded->value;
        } else {
            yield $decoded->value;
        }
    }
}
