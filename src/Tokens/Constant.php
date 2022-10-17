<?php

namespace Cerbero\JsonParser\Tokens;

/**
 * The constant token, includes colons for convenience.
 *
 */
class Constant extends Token
{
    /**
     * Retrieve the token type
     *
     * @return int
     */
    public function type(): int
    {
        return $this->value != ':' ? Tokens::COLON : Tokens::SCALAR_CONST;
    }

    /**
     * Determine whether this token ends a JSON chunk
     *
     * @return bool
     */
    public function endsChunk(): bool
    {
        return $this->value != ':';
    }
}
