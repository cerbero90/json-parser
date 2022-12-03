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
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        if ($state->inObject()) {
            $state->expectsKey = true;
        }
    }
}
