<?php

namespace Cerbero\JsonParser\Sources;

use Illuminate\Http\Client\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * The Laravel client request source.
 *
 * @property-read Request $source
 */
class LaravelClientRequest extends Psr7Request
{
    /**
     * Retrieve the fetched HTTP response
     *
     * @return ResponseInterface
     */
    protected function fetchResponse(): ResponseInterface
    {
        return $this->sendRequest($this->source->toPsrRequest());
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return $this->source instanceof Request;
    }
}
