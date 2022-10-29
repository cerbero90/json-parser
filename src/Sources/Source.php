<?php

namespace Cerbero\JsonParser\Sources;

use Cerbero\JsonParser\Config;
use Cerbero\JsonParser\Exceptions\SourceException;
use IteratorAggregate;
use Traversable;

/**
 * The JSON source.
 *
 */
abstract class Source implements IteratorAggregate
{
    /**
     * The registered custom sources.
     *
     * @var array
     */
    protected static array $customSources = [];

    /**
     * The cached size of the JSON source.
     *
     * @var int|null
     */
    protected int $size;

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable
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
    protected function __construct(protected mixed $source, protected Config $config)
    {
    }

    /**
     * Instantiate the class statically
     *
     * @param mixed $source
     * @param Config $config
     * @return static
     */
    public static function from(mixed $source, Config $config): static
    {
        return new static($source, $config);
    }

    /**
     * Register the given custom sources
     *
     * @param string ...$customSource
     * @return void
     */
    public function register(string ...$customSource): void
    {
        foreach ($customSource as $class) {
            if (!is_subclass_of($class, Source::class)) {
                throw SourceException::invalidSource($class);
            }

            static::$customSources[] = $class;
        }
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
