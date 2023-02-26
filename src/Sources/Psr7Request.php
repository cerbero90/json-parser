<?php

namespace Cerbero\JsonParser\Sources;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The PSR-7 request source.
 *
 * @property-read RequestInterface $source
 */
class Psr7Request extends Endpoint
{
    /**
     * Retrieve the fetched HTTP response
     *
     * @return ResponseInterface
     */
    protected function fetchResponse(): ResponseInterface
    {
        return $this->sendRequest($this->source);
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return $this->source instanceof RequestInterface;
    }
}
