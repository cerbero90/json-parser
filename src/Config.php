<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\ArrayDecoder;
use Cerbero\JsonParser\Decoders\Decoder;
use Closure;

/**
 * The configuration.
 *
 */
class Config
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
     * @var int
     */
    public int $bytes = 1024 * 8;

    /**
     * The callback to run during a parsing error.
     *
     * @var Closure|null
     */
    public ?Closure $onError = null;

    /**
     * Instantiate the class
     *
     */
    public function __construct()
    {
        $this->decoder = new ArrayDecoder();
        $this->onError = fn () => true;
    }
}
