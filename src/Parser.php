<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\ConfigurableDecoder;
use Cerbero\JsonParser\Exceptions\SyntaxException;
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
            if (!$token->matches($this->state->expectedToken)) {
                throw new SyntaxException($token, $this->lexer->position());
            }

            $this->state->mutateByToken($token);

            if (!$token->endsChunk() || $this->state->treeIsDeep()) {
                continue;
            }

            if ($this->state->hasBuffer()) {
                /** @var string|int $key */
                $key = $this->decoder->decode($this->state->key());
                $value = $this->decoder->decode($this->state->value());

                yield $key => $this->state->callPointer($value, $key);
            }

            if ($this->state->canStopParsing()) {
                break;
            }
        }
    }

    /**
     * Retrieve the parsing progress
     *
     * @return Progress
     */
    public function progress(): Progress
    {
        return $this->lexer->progress();
    }
}
