<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Tokens\Token;
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
     * Instantiate the class.
     *
     * @param Lexer $lexer
     * @param Config $config
     */
    public function __construct(protected Lexer $lexer, protected Config $config)
    {
        $this->state = new State();
        $this->pointers = new Pointers(...$config->pointers);
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        $this->state->matchPointer($this->pointers);

        foreach ($this->lexer as $token) {
            $this->handleToken($token);
            $this->rematchPointer();

            if (!$token->endsChunk() || $this->state->treeIsDeep()) {
                continue;
            }

            if ($this->state->hasBuffer() && $this->state->inObject()) {
                yield $this->decode($this->state->key()) => $this->decode($this->state->pullBuffer());
            } elseif ($this->state->hasBuffer() && !$this->state->inObject()) {
                yield $this->decode($this->state->pullBuffer());
            }

            $this->markPointerAsFound();

            if ($this->pointers->wereFound() && !$this->state->inPointer()) {
                break;
            }
        }
    }

    /**
     * Handle the given token
     *
     * @param Token $token
     * @return void
     */
    protected function handleToken(Token $token): void
    {
        if ($token->isValue() && !$this->state->inObject() && $this->state->treeIsShallow()) {
            $this->state->traverseArray();
        }

        if ($this->state->inRoot() && $this->state->shouldBufferToken($token)) {
            $this->state->bufferToken($token);
        }

        $token->mutateState($this->state);
    }

    /**
     * Set the matching JSON pointer when the tree changes
     *
     * @return void
     */
    protected function rematchPointer(): void
    {
        if ($this->state->treeChanged() && $this->pointers->count() > 1) {
            $this->state->matchPointer($this->pointers);
            $this->state->treeDidNotChange();
        }
    }

    /**
     * Retrieve the decoded value of the given JSON fragment
     *
     * @param string $json
     * @return mixed
     */
    protected function decode(string $json): mixed
    {
        $decoded = $this->config->decoder->decode($json);

        if (!$decoded->succeeded) {
            call_user_func($this->config->onError, $decoded);
        }

        return $decoded->value;
    }

    /**
     * Mark the matching JSON pointer as found
     *
     * @return void
     */
    protected function markPointerAsFound(): void
    {
        if ($this->state->pointerMatchesTree()) {
            $this->pointers->markAsFound($this->state->pointer());
        }
    }
}
