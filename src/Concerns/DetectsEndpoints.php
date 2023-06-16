<?php

namespace Cerbero\JsonParser\Concerns;

use function is_array;
use function in_array;

/**
 * The trait to detect endpoints.
 *
 */
trait DetectsEndpoints
{
    /**
     * Determine whether the given value points to an endpoint
     *
     * @param string $value
     * @return bool
     */
    protected function isEndpoint(string $value): bool
    {
        return is_array($url = parse_url($value))
            && in_array($url['scheme'] ?? null, ['http', 'https'])
            && isset($url['host']);
    }
}
