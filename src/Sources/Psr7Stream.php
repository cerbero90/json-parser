<?php

namespace Cerbero\JsonParser\Sources;

use Psr\Http\Message\StreamInterface;
use Traversable;

use function in_array;

/**
 * The PSR-7 stream source.
 *
 * @property-read StreamInterface $source
 */
class Psr7Stream extends Source
{
    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    public function getIterator(): Traversable
    {
        if (!in_array(StreamWrapper::NAME, stream_get_wrappers())) {
            stream_wrapper_register(StreamWrapper::NAME, StreamWrapper::class);
        }

        $stream = fopen(StreamWrapper::NAME . '://stream', 'rb', false, stream_context_create([
            StreamWrapper::NAME => ['stream' => $this->source],
        ]));

        return new JsonResource($stream, $this->config);
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return $this->source instanceof StreamInterface;
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return $this->source->getSize();
    }
}
