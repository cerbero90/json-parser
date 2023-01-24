<?php

namespace Cerbero\JsonParser\Sources;

use Traversable;

use function is_string;
use function is_resource;

/**
 * The resource source.
 *
 * @property-read resource $source
 */
class JsonResource extends Source
{
    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    public function getIterator(): Traversable
    {
        while (!feof($this->source)) {
            if (is_string($chunk = fread($this->source, $this->config->bytes))) {
                yield $chunk;
            }
        }
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return is_resource($this->source);
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
