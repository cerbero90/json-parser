<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\State;

/**
 * The token that begins compound data (JSON arrays or objects).
 *
 */
final class CompoundBegin extends Token
{
    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        $state->expectsKey = $beginsObject = $this->value == '{';
        $state->expectedToken = $beginsObject ? Tokens::AFTER_OBJECT_BEGIN : Tokens::AFTER_ARRAY_BEGIN;
        $state->tree()->deepen($beginsObject);
    }
}
