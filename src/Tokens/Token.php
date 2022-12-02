<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\State;
use Stringable;

/**
 * The abstract implementation of a token.
 *
 */
abstract class Token implements Stringable
{
    /**
     * The token value.
     *
     * @var string
     */
    protected string $value;

    /**
     * Retrieve the token type
     *
     * @return int
     */
    abstract public function type(): int;

    /**
     * Set the token value
     *
     * @param string $value
     * @return static
     */
    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Determine whether the token is a value
     *
     * @return bool
     */
    public function isValue(): bool
    {
        return ($this->type() | Tokens::VALUE_ANY) == Tokens::VALUE_ANY;
    }

    /**
     * Determine whether the token is a scalar value
     *
     * @return bool
     */
    public function isScalar(): bool
    {
        return ($this->type() | Tokens::VALUE_SCALAR) == Tokens::VALUE_SCALAR;
    }

    /**
     * Determine whether the token is a string
     *
     * @return bool
     */
    public function isString(): bool
    {
        return ($this->type() | Tokens::SCALAR_STRING) == Tokens::SCALAR_STRING;
    }

    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        $state->treeDidNotChange();

        if ($this->isValue() && !$state->inObject() && $state->shouldTrackTree()) {
            $state->traverseArray();
        }

        if ($this->isString() && $state->expectsKey() && $state->shouldTrackTree()) {
            $state->traverseKey($this);
        }

        if ($state->inRoot() && $state->shouldBufferToken($this)) {
            $state->bufferToken($this);
        }
    }

    /**
     * Determine whether this token ends a JSON chunk
     *
     * @return bool
     */
    public function endsChunk(): bool
    {
        return false;
    }

    /**
     * Retrieve the underlying token value
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
