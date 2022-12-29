<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\ConfigurableDecoder;
use Cerbero\JsonParser\Sources\Source;
use IteratorAggregate;
use Traversable;

/**
 * The JSON parser.
 *
 * @implements IteratorAggregate<string|int, mixed>
 */
final class Parser implements IteratorAggregate
{
    /**
     * The JSON parsing state.
     *
     * @var State
     */
    private State $state;

    /**
     * The decoder handling potential errors.
     *
     * @var ConfigurableDecoder
     */
    private ConfigurableDecoder $decoder;

    /**
     * Instantiate the class.
     *
     * @param Lexer $lexer
     * @param Config $config
     */
    public function __construct(private Lexer $lexer, private Config $config)
    {
        $this->state = new State();
        $this->decoder = new ConfigurableDecoder($config);
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
     * @return Traversable<string|int, mixed>
     */
    public function getIterator(): Traversable
    {
        $this->state->setPointers(...$this->config->pointers);

        foreach ($this->lexer as $token) {
            $this->state->mutateByToken($token);

            if (!$token->endsChunk() || $this->state->treeIsDeep()) {
                continue;
            }

            if ($this->state->hasBuffer() && $this->state->inObject()) {
                yield $this->decoder->decode($this->state->key()) => $this->decoder->decode($this->state->value());
            } elseif ($this->state->hasBuffer() && !$this->state->inObject()) {
                yield $this->decoder->decode($this->state->value());
            }

            if ($this->state->canStopParsing()) {
                break;
            }
        }
    }
}
