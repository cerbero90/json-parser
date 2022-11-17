<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\State;

/**
 * The comma token.
 *
 */
class Comma extends Token
{
    /**
     * Retrieve the token type
     *
     * @return int
     */
    public function type(): int
    {
        return Tokens::COMMA;
    }

    /**
     * Update the given state
     *
     * @param State $state
     * @return void
     */
    protected function updateState(State $state): void
    {
        if ($state->inObject()) {
            $state->expectKey();
        }
    }
}
