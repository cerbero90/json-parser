<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Decoders\Decoder;
use Cerbero\JsonParser\Exceptions\SyntaxException;
use Cerbero\JsonParser\Pointers\Pointer;
use Cerbero\JsonParser\Sources\AnySource;
use Cerbero\JsonParser\Tokens\Lexer;
use Cerbero\JsonParser\Tokens\Parser;
use Cerbero\JsonParser\ValueObjects\Config;
use Cerbero\JsonParser\ValueObjects\Progress;
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
    private readonly Config $config;

    /**
     * The lexer.
     *
     * @var Lexer
     */
    private readonly Lexer $lexer;

    /**
     * The parser.
     *
     * @var Parser
     */
    private readonly Parser $parser;

    /**
     * Instantiate the class statically
     *
     * @param mixed $source
     * @return self
     */
    public static function parse(mixed $source): self
    {
        return new self($source);
    }

    /**
     * Instantiate the class.
     *
     * @param mixed $source
     */
    public function __construct(mixed $source)
    {
        $this->config = new Config();
        $this->lexer = new Lexer(new AnySource($source, $this->config));
        $this->parser = new Parser($this->lexer->getIterator(), $this->config);
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
            $e->setPosition($this->lexer->position());
            ($this->config->onSyntaxError)($e);
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
        $this->config->pointers->add(new Pointer($pointer, false, $callback));

        return $this;
    }

    /**
     * Set the lazy JSON pointers
     *
     * @param string[]|array<string, Closure> $pointers
     * @return self
     */
    public function lazyPointers(array $pointers): self
    {
        foreach ($pointers as $pointer => $callback) {
            $callback instanceof Closure ? $this->lazyPointer($pointer, $callback) : $this->lazyPointer($callback);
        }

        return $this;
    }

    /**
     * Set a lazy JSON pointer
     *
     * @param string $pointer
     * @param Closure|null $callback
     * @return self
     */
    public function lazyPointer(string $pointer, Closure $callback = null): self
    {
        $this->config->pointers->add(new Pointer($pointer, true, $callback));

        return $this;
    }

    /**
     * Set a lazy JSON pointer for the whole JSON
     *
     * @return self
     */
    public function lazy(): self
    {
        return $this->lazyPointer('');
    }

    /**
     * Traverse the JSON one key and value at a time
     *
     * @param Closure|null $callback
     * @return void
     */
    public function traverse(Closure $callback = null): void
    {
        foreach ($this as $key => $value) {
            $callback && $callback($value, $key, $this);
        }
    }

    /**
     * Eager load the JSON into an array
     *
     * @return array<string|int, mixed>
     */
    public function toArray(): array
    {
        return $this->parser->toArray();
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
        return $this->lexer->progress();
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

    /**
     * Set the logic to run for wrapping the parser
     *
     * @param Closure $callback
     * @return self
     */
    public function wrap(Closure $callback): self
    {
        $this->config->wrapper = $callback;

        return $this;
    }
}
