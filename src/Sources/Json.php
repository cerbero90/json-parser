<?php

namespace Cerbero\JsonParser\Sources;

use Cerbero\JsonParser\Concerns\DetectsEndpoints;
use Traversable;

use function is_string;
use function strlen;

/**
 * The JSON source.
 *
 * @property-read string $source
 */
class Json extends Source
{
    use DetectsEndpoints;

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    public function getIterator(): Traversable
    {
        for ($i = 0; $i < $this->size(); $i += $this->config->bytes) {
            yield substr($this->source, $i, $this->config->bytes);
        }
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return is_string($this->source) && !is_file($this->source) && !$this->isEndpoint($this->source);
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return strlen($this->source);
    }
}
