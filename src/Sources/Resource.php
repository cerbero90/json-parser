<?php

namespace Cerbero\JsonParser\Sources;

use Traversable;

/**
 * The resource source.
 *
 */
class Resource extends Source
{
    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    public function getIterator(): Traversable
    {
        while (!feof($this->source)) {
            yield fread($this->source, $this->config->bytes);
        }
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return is_resource($this->source) || get_resource_type($this->source) == 'stream';
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        $stats = fstat($this->source);
        $size = $stats['size'] ?? null;

        return $size ?: null;
    }
}
