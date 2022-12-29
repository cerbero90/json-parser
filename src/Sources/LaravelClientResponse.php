<?php

namespace Cerbero\JsonParser\Sources;

use Illuminate\Http\Client\Response;
use Traversable;

/**
 * The Laravel client response source.
 *
 * @property-read Response $source
 */
class LaravelClientResponse extends Source
{
    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    public function getIterator(): Traversable
    {
        return new Psr7Message($this->source->toPsrResponse(), $this->config);
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return $this->source instanceof Response;
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return $this->source->toPsrResponse()->getBody()->getSize();
    }
}
