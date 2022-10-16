<?php

namespace Cerbero\JsonParser\Tokens;

/**
 * The abstract implementation of a token.
 *
 */
abstract class Token
{
    /**
     * Determine whether this token closes a JSON chunk
     *
     * @return bool
     */
    public function closesChunk(): bool
    {
        return false;
    }
}
