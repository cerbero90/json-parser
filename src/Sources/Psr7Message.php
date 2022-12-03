<?php

namespace Cerbero\JsonParser\Sources;

use Psr\Http\Message\MessageInterface;
use Traversable;

/**
 * The PSR-7 message source.
 *
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
        return Psr7Stream::from($this->source->getBody(), $this->config);
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return $this->source instanceof MessageInterface;
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
