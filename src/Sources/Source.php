<?php

namespace Cerbero\JsonParser\Sources;

use Cerbero\JsonParser\Config;
use IteratorAggregate;
use Traversable;

/**
 * The JSON source.
 *
 * @implements IteratorAggregate<int, string>
 */
abstract class Source implements IteratorAggregate
{
    /**
     * The cached size of the JSON source.
     *
     * @var int|null
     */
    protected ?int $size;

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     */
    abstract public function getIterator(): Traversable;

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    abstract public function matches(): bool;

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    abstract protected function calculateSize(): ?int;

    /**
     * Enforce the factory method to instantiate the class.
     *
     * @param mixed $source
     * @param Config $config
     */
    final public function __construct(protected mixed $source, protected Config $config)
    {
    }

    /**
     * Retrieve the underlying configuration
     *
     * @return Config
     */
    public function config(): Config
    {
        return $this->config;
    }

    /**
     * Retrieve the size of the JSON source and cache it
     *
     * @return int|null
     */
    public function size(): ?int
    {
        return $this->size ??= $this->calculateSize();
    }
}
