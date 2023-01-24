<?php

namespace Cerbero\JsonParser\Sources;

use Traversable;

use function is_string;

/**
 * The filename source.
 *
 * @property-read string $source
 */
class Filename extends Source
{
    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    public function getIterator(): Traversable
    {
        $handle = fopen($this->source, 'rb');

        try {
            yield from new JsonResource($handle, $this->config);
        } finally {
            $handle && fclose($handle);
        }
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return is_string($this->source) && is_file($this->source);
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return filesize($this->source) ?: null;
    }
}
