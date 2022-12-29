<?php

namespace Cerbero\JsonParser\Sources;

use Traversable;

/**
 * The custom source.
 *
 * @property-read Source $source
 */
class CustomSource extends Source
{
    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    public function getIterator(): Traversable
    {
        yield from $this->source;
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return $this->source instanceof Source;
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return $this->source->size();
    }
}
