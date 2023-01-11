<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\Decoder;
use Cerbero\JsonParser\Decoders\ObjectDecoder;
use Cerbero\JsonParser\Pointers\Pointer;
use Cerbero\JsonParser\Sources\AnySource;
use Closure;
use IteratorAggregate;
use Traversable;

/**
 * The JSON parser entry-point.
 *
 * @implements IteratorAggregate<string|int, mixed>
 */
final class JsonParser implements IteratorAggregate
{
    /**
     * The configuration.
     *
     * @var Config
     */
    private Config $config;

    /**
     * The parser.
     *
     * @var Parser
     */
    private Parser $parser;

    /**
     * Instantiate the class.
     *
     * @param mixed $source
     */
    public function __construct(mixed $source)
    {
        $this->config = new Config();
        $this->parser = Parser::for(new AnySource($source, $this->config));
    }

    /**
     * Statically instantiate the class
     *
     * @param mixed $source
     * @return static
     */
    public static function parse(mixed $source): static
    {
        return new static($source);
    }

    /**
     * Set the JSON decoder to turn a JSON into objects
     *
     * @return static
     */
    public function toObjects(): static
    {
        return $this->decoder(new ObjectDecoder());
    }

    /**
     * Set the JSON decoder
     *
     * @param Decoder $decoder
     * @return static
     */
    public function decoder(Decoder $decoder): static
    {
        $this->config->decoder = $decoder;

        return $this;
    }

    /**
     * Set the JSON pointers
     *
     * @param string[]|array<string, Closure> $pointers
     * @return static
     */
    public function pointers(array $pointers): static
    {
        foreach ($pointers as $pointer => $callback) {
            $callback instanceof Closure ? $this->pointer($pointer, $callback) : $this->pointer($callback);
        }

        return $this;
    }

    /**
     * Set a JSON pointer
     *
     * @param string $pointer
     * @param Closure|null $callback
     * @return static
     */
    public function pointer(string $pointer, Closure $callback = null): static
    {
        $this->config->pointers[] = new Pointer($pointer, $callback);

        return $this;
    }

    /**
     * The number of bytes to read in each chunk
     *
     * @param int<1, max> $bytes
     * @return static
     */
    public function bytes(int $bytes): static
    {
        $this->config->bytes = $bytes;

        return $this;
    }

    /**
     * Silence errors while parsing
     *
     * @return static
     */
    public function ignoreErrors(): static
    {
        return $this->onError(fn () => true);
    }

    /**
     * Set the logic to run during parsing errors
     *
     * @param Closure $callback
     * @return static
     */
    public function onError(Closure $callback): static
    {
        $this->config->onError = $callback;

        return $this;
    }

    /**
     * Retrieve the lazily iterable JSON
     *
     * @return Traversable<string|int, mixed>
     */
    public function getIterator(): Traversable
    {
        return $this->parser;
    }
}
