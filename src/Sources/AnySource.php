<?php

namespace Cerbero\JsonParser\Sources;

use Cerbero\JsonParser\Exceptions\SourceException;
use Generator;
use Traversable;

/**
 * The handler of any JSON source.
 *
 */
class AnySource extends Source
{
    /**
     * The supported sources.
     *
     * @var array
     */
    protected array $supportedSources = [
        CustomSource::class,
        Endpoint::class,
        Filename::class,
        IterableSource::class,
        JsonString::class,
        LaravelClientResponse::class,
        Psr7Message::class,
        Psr7Stream::class,
        Resource::class,
    ];

    /**
     * The matching source.
     *
     * @var Source
     */
    protected Source $matchingSource;

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        foreach ($this->sources() as $source) {
            if ($source->matches()) {
                return $this->matchingSource = $source;
            }
        }

        throw SourceException::unsupported();
    }

    /**
     * Retrieve all available sources
     *
     * @return Source[]
     */
    protected function sources(): Generator
    {
        foreach (static::$customSources as $source) {
            yield $source::from($this->source, $this->config);
        }

        foreach ($this->supportedSources as $source) {
            yield $source::from($this->source, $this->config);
        }
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return true;
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return $this->matchingSource?->size();
    }
}
