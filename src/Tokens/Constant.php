<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\ValueObjects\State;

/**
 * The constant token.
 *
 */
final class Constant extends Token
{
    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        $state->expectedToken = $state->tree->inObject() ? Tokens::AFTER_OBJECT_VALUE : Tokens::AFTER_ARRAY_VALUE;
    }

    /**
     * Determine whether this token ends a JSON chunk
     *
     * @return bool
     */
    public function endsChunk(): bool
    {
        return true;
    }
}
