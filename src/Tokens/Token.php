<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\ValueObjects\State;
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
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    abstract public function mutateState(State $state): void;

    /**
     * Determine whether this token matches the given type
     *
     * @param int $type
     * @return bool
     */
    public function matches(int $type): bool
    {
        return (Tokens::TYPES[$this->value[0]] & $type) != 0;
    }

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
        return (Tokens::TYPES[$this->value[0]] | Tokens::VALUE_ANY) == Tokens::VALUE_ANY;
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
