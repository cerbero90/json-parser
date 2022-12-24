<?php

namespace Cerbero\JsonParser\Concerns;

/**
 * The trait to detect endpoints.
 *
 */
trait DetectsEndpoints
{
    /**
     * Determine whether the given string points to an endpoint
     *
     * @param string $string
     * @return bool
     */
    public function isEndpoint(string $string): bool
    {
        if (($url = parse_url($string)) === false) {
            return false;
        }

        return isset($url['host']) && in_array($url['scheme'] ?? null, ['http', 'https']);
    }
}
