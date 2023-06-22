<?php

namespace Cerbero\JsonParser\Tokens;

use ArrayAccess;
use Cerbero\JsonParser\Decoders\ConfigurableDecoder;
use Cerbero\JsonParser\Exceptions\NodeNotFoundException;
use Cerbero\JsonParser\Exceptions\SyntaxException;
use Cerbero\JsonParser\ValueObjects\Config;
use Cerbero\JsonParser\ValueObjects\State;
use Generator;
use Iterator;

/**
 * The JSON parser.
 *
 * @implements Iterator<string|int, mixed>
 */
final class Parser implements Iterator, ArrayAccess
{
    /**
     * The decoder handling potential errors.
     *
     * @var ConfigurableDecoder
     */
    private readonly ConfigurableDecoder $decoder;

    /**
     * The state.
     *
     * @var State
     */
    private State $state;

    /**
     * The current key.
     *
     * @var string|int|null
     */
    private string|int|null $key = null;

    /**
     * The current compound to lazy load.
     *
     * @var self|null
     */
    private ?self $lazyLoad = null;

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
     * Track the parsing state
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->state ??= new State($this->config->pointers, fn () => clone $this);

        // $this->state ??= new State(
        //     $this->config->pointers,
        //     fn () => new self($this->lazyLoad(), clone $this->config, $this->state),
        // );

        // $this->state ??= new State(
        //     $this->config->pointers,
        //     fn () => new self($this->lazyLoad(), clone $this->config),
        // );
    }

    /**
     * Determine whether there are more tokens to parse
     *
     * @return bool
     */
    public function valid(): bool
    {
        if (!$this->tokens->valid() || $this->state->canStopParsing()) {
            return false;
        }

        if ($this->isFastForwarding) {
            return true;
        }

        $token = $this->tokens->current();

        if (!$token->matches($this->state->expectedToken)) {
            throw new SyntaxException($token);
        }

        $this->state->mutateByToken($token);

        if ($token->endsChunk() && $this->state->shouldYield()) {
            return true;
        }

        $this->tokens->next();

        return $this->valid();
    }

    /**
     * Retrieve the current value
     *
     * @return mixed
     */
    public function current(): mixed
    {
        if ($this->isFastForwarding) {
            return null;
        }

        $this->key = $this->decoder->decode($this->state->tree->currentKey());
        $value = $this->decoder->decode($this->state->value());

        if ($value instanceof self) {
            $this->lazyLoad = $value;
        }

        return $this->state->callPointer($value, $this->key);
    }

    /**
     * Retrieve the current key
     *
     * @return mixed
     */
    public function key(): mixed
    {
        if ($this->isFastForwarding) {
            return null;
        }

        $key = $this->key;
        $this->key = null;

        return $key;
    }

    /**
     * Move the pointer to the next token
     *
     * @return void
     */
    public function next(): void
    {
        if ($this->lazyLoad) {
            $this->lazyLoad->fastForward();
            $this->lazyLoad = null;
        }

        $this->tokens->next();
    }

    /**
     * Lazy load the current compound
     *
     * @param Generator<int, Token> $tokens
     * @return Generator<int, Token>
     */
    private function lazyLoad(Generator $tokens): Generator
    {
        $depth = 0;

        do {
            yield $token = $tokens->current();

            if ($token instanceof CompoundBegin) {
                $token->shouldLazyLoad = true;
                $depth++;
            } elseif ($token instanceof CompoundEnd) {
                $token->shouldLazyLoad = true;
                $depth--;
            }

            $depth > 0 && $tokens->next();
        } while ($depth > 0);
    }

    /**
     * Eager load the current compound into an array
     *
     * @return array<string|int, mixed>
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this as $key => $value) {
            $array[$key] = $value instanceof self ? $value->toArray() : $value;
        }

        return $array;
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

    /**
     * Determine whether a node exists: not allowed
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return false;
    }

    /**
     * Retrieve a node dynamically
     *
     * @param mixed $offset
     * @return mixed
     * @throws NodeNotFoundException
     */
    public function offsetGet(mixed $offset): mixed
    {
        foreach ($this as $key => $value) {
            if ($key === $offset) {
                !$value instanceof self && $this->tokens->next();
                // $value instanceof self ? $this->lazyLoad->fastForward() : $this->tokens->next();

                return $value;
            }
        }

        throw new NodeNotFoundException($offset);
    }

    /**
     * Set a node: not allowed
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        return;
    }

    /**
     * Unset a node: not allowed
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        return;
    }

    /**
     * Retrieve a node dynamically
     *
     * @param string $name
     * @return mixed
     * @throws NodeNotFoundException
     */
    public function __get(string $name): mixed
    {
        return $this->offsetGet($name);
    }

    /**
     * Clone the parser
     *
     * @return void
     */
    public function __clone(): void
    {
        $this->tokens = $this->lazyLoad($this->tokens);
        $this->config = clone $this->config;
        $this->lazyLoad = null;
        $this->key = null;
    }
}
