<?php

namespace Cerbero\JsonParser\Sources;

use Traversable;

use function is_array;
use function count;

/**
 * The iterable source.
 *
 * @property-read iterable $source
 */
class IterableSource extends Source
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
        return is_iterable($this->source) && !$this->source instanceof Source;
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return is_array($this->source) ? count($this->source) : iterator_count(clone $this->source);
    }
}
