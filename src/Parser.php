<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\Decoder;
use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Tokens\Token;
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
     * The JSON decoder.
     *
     * @var Decoder
     */
    protected Decoder $decoder;

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
        $this->decoder = $config->decoder;
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

            if ($this->state->hasBuffer()) {
                yield from $this->yieldDecodedBuffer();
            }

            $this->markPointerAsFound();

            if ($this->pointers->wereFound() && !$this->state->treeInPointer()) {
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
    public function handleToken(Token $token): void
    {
        $token->mutateState($this->state);

        if ($token->isValue() && !$this->state->inObject() && $this->state->treeIsShallow()) {
            $this->state->traverseArray();
        }

        if ($this->state->shouldBufferToken($token)) {
            $this->state->bufferToken($token);
        }
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
     * Yield the decoded JSON of the buffer
     *
     * @return Generator
     */
    protected function yieldDecodedBuffer(): Generator
    {
        $decoded = $this->decoder->decode($this->state->pullBuffer());

        if (!$decoded->succeeded) {
            call_user_func($this->config->onError, $decoded);
        }

        if ($this->state->inObject()) {
            yield $this->state->node() => $decoded->value;
        } else {
            yield $decoded->value;
        }
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
