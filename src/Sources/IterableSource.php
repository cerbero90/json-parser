<?php

namespace Cerbero\JsonParser\Sources;

use Traversable;

/**
 * The iterable source.
 *
 */
class IterableSource extends Source
{
    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable
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
        return is_iterable($this->source);
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return iterator_count(clone $this->source);
    }
}
