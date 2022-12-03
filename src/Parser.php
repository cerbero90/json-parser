<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\ErrorHandlingDecoder;
use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Sources\Source;
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
     * The decoder handling potential errors.
     *
     * @var ErrorHandlingDecoder
     */
    protected ErrorHandlingDecoder $decoder;

    /**
     * Instantiate the class.
     *
     * @param Lexer $lexer
     * @param Config $config
     */
    public function __construct(protected Lexer $lexer, protected Config $config)
    {
        $this->state = new State();
        $this->decoder = new ErrorHandlingDecoder($config);
    }

    /**
     * Instantiate the class statically
     *
     * @param Source $source
     * @return static
     */
    public static function for(Source $source): static
    {
        return new static(new Lexer($source), $source->config());
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        $this->pointers = new Pointers(...$this->config->pointers);
        $this->state->matchPointer($this->pointers);

        foreach ($this->lexer as $token) {
            $token->mutateState($this->state);
            $this->rematchPointer();

            if (!$token->endsChunk() || $this->state->treeIsDeep()) {
                continue;
            }

            if ($this->state->hasBuffer() && $this->state->inObject()) {
                yield $this->decoder->decode($this->state->key()) => $this->decoder->decode($this->state->pullBuffer());
            } elseif ($this->state->hasBuffer() && !$this->state->inObject()) {
                yield $this->decoder->decode($this->state->pullBuffer());
            }

            $this->markPointerAsFound();

            if ($this->pointers->wereFound() && !$this->state->inPointer()) {
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
        if ($this->state->treeChanged() && $this->pointers->count() > 1) {
            $this->state->matchPointer($this->pointers);
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
