<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\State;

/**
 * The double quote token.
 *
 */
class DoubleQuote extends Token
{
    /**
     * Whether this token is an object key.
     *
     * @var bool
     */
    protected bool $isKey;

    /**
     * Retrieve the token type
     *
     * @return int
     */
    public function type(): int
    {
        return Tokens::SCALAR_STRING;
    }

    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        if (!$this->isKey = $state->expectsKey()) {
            return;
        }

        $state->doNotExpectKey();

        if ($state->treeIsShallow()) {
            $state->traverseTree($this->key());
        }
    }

    /**
     * Retrieve the object key
     *
     * @return string
     */
    protected function key(): string
    {
        return substr($this->value, 1, -1);
    }

    /**
     * Determine whether this token ends a JSON chunk
     *
     * @return bool
     */
    public function endsChunk(): bool
    {
        return !$this->isKey;
    }
}