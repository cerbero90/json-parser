<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\Decoders\ConfigurableDecoder;
use Cerbero\JsonParser\Exceptions\SyntaxException;
use Cerbero\JsonParser\Tokens\CompoundBegin;
use Cerbero\JsonParser\Tokens\CompoundEnd;
use Cerbero\JsonParser\Tokens\Token;
use Cerbero\JsonParser\ValueObjects\Config;
use Cerbero\JsonParser\ValueObjects\State;
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
     * The decoder handling potential errors.
     *
     * @var ConfigurableDecoder
     */
    private readonly ConfigurableDecoder $decoder;

    /**
     * Whether the parser is fast-forwarding.
     *
     * @var bool
     */
    private bool $isFastForwarding = false;

    /**
     * Instantiate the class.
     *
     * @param Generator<int, Token> $tokens
     * @param Config $config
     */
    public function __construct(private readonly Generator $tokens, private readonly Config $config)
    {
        $this->decoder = new ConfigurableDecoder($config);
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

            if (!$token->endsChunk() || $state->tree->isDeep()) {
                continue;
            }

            if ($state->hasBuffer()) {
                /** @var string|int $key */
                $key = $this->decoder->decode($state->tree->currentKey());
                $value = $this->decoder->decode($state->value());
                $wrapper = $value instanceof self ? ($this->config->wrapper)($value) : $value;

                yield $key => $state->callPointer($wrapper, $key);

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
     * Eager load the current compound into an array
     *
     * @return array<string|int, mixed>
     */
    public function toArray(): array
    {
        $index = 0;
        $array = [];
        $hasWildcards = false;

        foreach ($this as $key => $value) {
            if (isset($array[$index][$key])) {
                $index++;
                $hasWildcards = true;
            }

            $turnsIntoArray = is_object($value) && method_exists($value, 'toArray');
            $array[$index][$key] = $turnsIntoArray ? $value->toArray() : $value;
        }

        return $hasWildcards || empty($array) ? $array : $array[0];
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
            $value instanceof self && $value->fastForward(); // @codeCoverageIgnore
        }
    }
}
