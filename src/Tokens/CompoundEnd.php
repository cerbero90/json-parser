<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\ValueObjects\State;

/**
 * The token that ends compound data (JSON arrays or objects).
 *
 */
final class CompoundEnd extends Token
{
    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        if (!$this->shouldLazyLoad) {
            $state->tree()->emerge();
        }

        $state->expectedToken = $state->tree()->inObject() ? Tokens::AFTER_OBJECT_VALUE : Tokens::AFTER_ARRAY_VALUE;
    }

    /**
     * Set the token value
     *
     * @param string $value
     * @return static
     */
    // public function setValue(string $value): static
    // {
    //     $this->shouldLazyLoad = false;

    //     return parent::setValue($value);
    // }

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
