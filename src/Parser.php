<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\ConfigurableDecoder;
use Cerbero\JsonParser\Exceptions\SyntaxException;
use Cerbero\JsonParser\Sources\Source;
use Cerbero\JsonParser\Tokens\CompoundBegin;
use Cerbero\JsonParser\Tokens\CompoundEnd;
use Cerbero\JsonParser\Tokens\Token;
use Generator;
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
     * The tokens to parse.
     *
     * @var Generator<int, Token>
     */
    private Generator $tokens;

    /**
     * The decoder handling potential errors.
     *
     * @var ConfigurableDecoder
     */
    private ConfigurableDecoder $decoder;

    /**
     * Whether the parser is fast-forwarding.
     *
     * @var bool
     */
    private bool $isFastForwarding = false;

    /**
     * Instantiate the class.
     *
     * @param Lexer|Generator<int, Token> $lexer
     * @param Config $config
     */
    public function __construct(private Lexer|Generator $lexer, private Config $config)
    {
        /** @phpstan-ignore-next-line */
        $this->tokens = $lexer instanceof Lexer ? $lexer->getIterator() : $lexer;
        $this->decoder = new ConfigurableDecoder($config);
    }

    /**
     * Instantiate the class statically
     *
     * @param Source $source
     * @return self
     */
    public static function for(Source $source): self
    {
        return new self(new Lexer($source), $source->config());
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<string|int, mixed>
     */
    public function getIterator(): Traversable
    {
        $state = new State($this->config->pointers, fn () => new self($this->lazyLoad(), clone $this->config));

        foreach ($this->tokens as $token) {
            if ($this->isFastForwarding) {
                continue;
            } elseif (!$token->matches($state->expectedToken)) {
                throw new SyntaxException($token);
            }

            $state->mutateByToken($token);

            if (!$token->endsChunk() || $state->tree()->isDeep()) {
                continue;
            }

            if ($state->hasBuffer()) {
                /** @var string|int $key */
                $key = $this->decoder->decode($state->key());
                $value = $this->decoder->decode($state->value());

                yield $key => $state->callPointer($value, $key);

                $value instanceof self && $value->fastForward();
            }

            if ($state->canStopParsing()) {
                break;
            }
        }
    }

    /**
     * Retrieve the generator to lazy load the current compound
     *
     * @return Generator<int, Token>
     */
    public function lazyLoad(): Generator
    {
        $depth = 0;

        do {
            yield $token = $this->tokens->current();

            if ($token instanceof CompoundBegin) {
                $depth++;
            } elseif ($token instanceof CompoundEnd) {
                $depth--;
            }

            $depth > 0 && $this->tokens->next();
        } while ($depth > 0);
    }

    /**
     * Fast-forward the parser
     *
     * @return void
     */
    public function fastForward(): void
    {
        if (!$this->tokens->valid()) {
            return;
        }

        $this->isFastForwarding = true;

        foreach ($this as $value) {
            $value instanceof self && $value->fastForward();
        }
    }

    /**
     * Retrieve the parsing progress
     *
     * @return Progress
     */
    public function progress(): Progress
    {
        /** @phpstan-ignore-next-line */
        return $this->lexer->progress();
    }

    /**
     * Retrieve the parsing position
     *
     * @return int
     */
    public function position(): int
    {
        /** @phpstan-ignore-next-line */
        return $this->lexer->position();
    }
}
