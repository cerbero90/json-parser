<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Decoders\Decoder;
use Cerbero\JsonParser\Exceptions\SyntaxException;
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
     * @return self
     */
    public static function parse(mixed $source): self
    {
        return new self($source);
    }

    /**
     * Retrieve the lazily iterable JSON
     *
     * @return Traversable<string|int, mixed>
     */
    public function getIterator(): Traversable
    {
        try {
            yield from $this->parser;
        } catch (SyntaxException $e) {
            call_user_func($this->config->onSyntaxError, $e);
        }
    }

    /**
     * Set the JSON pointers
     *
     * @param string[]|array<string, Closure> $pointers
     * @return self
     */
    public function pointers(array $pointers): self
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
     * @return self
     */
    public function pointer(string $pointer, Closure $callback = null): self
    {
        $this->config->pointers->add(new Pointer($pointer, $callback));

        return $this;
    }

    /**
     * Traverse the lazily iterable JSON
     *
     * @param Closure|null $callback
     * @return void
     */
    public function traverse(Closure $callback = null): void
    {
        $callback ??= fn () => true;

        foreach ($this as $key => $value) {
            $callback($value, $key, $this);
        }
    }

    /**
     * Set the JSON decoder
     *
     * @param Decoder $decoder
     * @return self
     */
    public function decoder(Decoder $decoder): self
    {
        $this->config->decoder = $decoder;

        return $this;
    }

    /**
     * Retrieve the parsing progress
     *
     * @return Progress
     */
    public function progress(): Progress
    {
        return $this->parser->progress();
    }

    /**
     * The number of bytes to read in each chunk
     *
     * @param int<1, max> $bytes
     * @return self
     */
    public function bytes(int $bytes): self
    {
        $this->config->bytes = $bytes;

        return $this;
    }

    /**
     * Set the patch to apply during a decoding error
     *
     * @param mixed $patch
     * @return self
     */
    public function patchDecodingError(mixed $patch = null): self
    {
        return $this->onDecodingError(function (DecodedValue $decoded) use ($patch) {
            $decoded->value = is_callable($patch) ? $patch($decoded) : $patch;
        });
    }

    /**
     * Set the logic to run during a decoding error
     *
     * @param Closure $callback
     * @return self
     */
    public function onDecodingError(Closure $callback): self
    {
        $this->config->onDecodingError = $callback;

        return $this;
    }

    /**
     * Set the logic to run during a syntax error
     *
     * @param Closure $callback
     * @return self
     */
    public function onSyntaxError(Closure $callback): self
    {
        $this->config->onSyntaxError = $callback;

        return $this;
    }
}
