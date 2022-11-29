<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\State;

/**
 * The token that begins compound data (JSON arrays or objects).
 *
 */
class CompoundBegin extends Token
{
    /**
     * Retrieve the token type
     *
     * @return int
     */
    public function type(): int
    {
        return Tokens::COMPOUND_BEGIN;
    }

    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        parent::mutateState($state);

        $state->tree()->deepen();

        if ($this->value == '{') {
            $state->expectKey();
        }
    }
}
