<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\JsonDecoder;
use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Decoders\Decoder;
use Cerbero\JsonParser\Decoders\SimdjsonDecoder;
use Cerbero\JsonParser\Exceptions\DecodingException;
use Cerbero\JsonParser\Exceptions\SyntaxException;
use Cerbero\JsonParser\Pointers\Pointer;
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
     * @var Pointer[]
     */
    public array $pointers = [];

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
     * Instantiate the class
     *
     */
    public function __construct()
    {
        $this->decoder = extension_loaded('simdjson') ? new SimdjsonDecoder() : new JsonDecoder();
        $this->onDecodingError = fn (DecodedValue $decoded) => throw new DecodingException($decoded);
        $this->onSyntaxError = fn (SyntaxException $e) => throw $e;
    }
}
