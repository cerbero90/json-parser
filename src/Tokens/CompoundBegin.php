<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\ValueObjects\State;

/**
 * The token that begins compound data (JSON arrays or objects).
 *
 */
final class CompoundBegin extends Token
{
    /**
     * Whether this compound should be lazy loaded.
     *
     * @var bool
     */
    // public bool $shouldLazyLoad = false;

    /**
     * Mutate the given state
     *
     * @param State $state
     * @return void
     */
    public function mutateState(State $state): void
    {
        $tree = $state->tree();

        // if ($this->shouldLazyLoad = $this->shouldLazyLoad && $tree->depth() >= 0) {
        if ($this->shouldLazyLoad = $state->isLazyLoading()) {
            // $state->expectedToken = $tree->inObject() ? Tokens::AFTER_OBJECT_VALUE : Tokens::AFTER_ARRAY_VALUE;
            return;
        }

        $state->expectsKey = $beginsObject = $this->value == '{';
        $state->expectedToken = $beginsObject ? Tokens::AFTER_OBJECT_BEGIN : Tokens::AFTER_ARRAY_BEGIN;
        $tree->deepen($beginsObject);
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
        return $this->shouldLazyLoad;
    }
}
