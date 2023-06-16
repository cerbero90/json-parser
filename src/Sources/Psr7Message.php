<?php

namespace Cerbero\JsonParser\Sources;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Traversable;

/**
 * The PSR-7 message source.
 *
 * @property-read MessageInterface $source
 */
class Psr7Message extends Source
{
    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    public function getIterator(): Traversable
    {
        return new Psr7Stream($this->source->getBody(), $this->config);
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return $this->source instanceof MessageInterface && !$this->source instanceof RequestInterface;
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return $this->source->getBody()->getSize();
    }
}
