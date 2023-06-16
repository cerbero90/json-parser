<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\ValueObjects\State;

/**
 * The colon token.
 *
 */
final class Colon extends Token
{
    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        $state->expectedToken = Tokens::VALUE_ANY;
    }
}
