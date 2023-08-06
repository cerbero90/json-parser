<?php

namespace Cerbero\JsonParser\ValueObjects;

use Cerbero\JsonParser\Decoders\JsonDecoder;
use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Decoders\Decoder;
use Cerbero\JsonParser\Decoders\SimdjsonDecoder;
use Cerbero\JsonParser\Exceptions\DecodingException;
use Cerbero\JsonParser\Exceptions\SyntaxException;
use Cerbero\JsonParser\Pointers\Pointer;
use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Tokens\Parser;
use Closure;

/**
 * The configuration.
 *
 */
final class Config
{
    /**
     * The JSON decoder.
     *
     * @var Decoder
     */
    public Decoder $decoder;

    /**
     * The JSON pointers.
     *
     * @var Pointers
     */
    public Pointers $pointers;

    /**
     * The number of bytes to read in each chunk.
     *
     * @var int<1, max>
     */
    public int $bytes = 1024 * 8;

    /**
     * The callback to run during a decoding error.
     *
     * @var Closure
     */
    public Closure $onDecodingError;

    /**
     * The callback to run during a syntax error.
     *
     * @var Closure
     */
    public Closure $onSyntaxError;

    /**
     * The callback to run for wrapping the parser.
     *
     * @var Closure
     */
    public Closure $wrapper;

    /**
     * Instantiate the class
     *
     */
    public function __construct()
    {
        $this->decoder = extension_loaded('simdjson') ? new SimdjsonDecoder() : new JsonDecoder();
        $this->pointers = new Pointers();
        $this->onDecodingError = fn (DecodedValue $decoded) => throw new DecodingException($decoded);
        $this->onSyntaxError = fn (SyntaxException $e) => throw $e;
        $this->wrapper = fn (Parser $parser) => $parser;
    }

    /**
     * Clone the configuration
     *
     * @return void
     */
    public function __clone(): void
    {
        $this->pointers = new Pointers();
        $this->pointers->add(new Pointer('', true));
    }
}
