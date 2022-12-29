<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\State;

/**
 * The token that ends compound data (JSON arrays or objects).
 *
 */
final class CompoundEnd extends Token
{
    /**
     * Retrieve the token type
     *
     * @return int
     */
    public function type(): int
    {
        return Tokens::COMPOUND_END;
    }

    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        $state->tree()->emerge();
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
