<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\State;

/**
 * The scalar string token.
 *
 */
class ScalarString extends Token
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
     * Update the given state
     *
     * @param State $state
     * @return void
     */
    protected function updateState(State $state): void
    {
        if (!$this->isKey = $state->expectsKey()) {
            return;
        }

        $state->doNotExpectKey();
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
