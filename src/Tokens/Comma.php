<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\ValueObjects\State;

/**
 * The comma token.
 *
 */
final class Comma extends Token
{
    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        $state->expectsKey = $state->tree->inObject();
        $state->expectedToken = $state->expectsKey ? Tokens::SCALAR_STRING : Tokens::VALUE_ANY;
    }
}
