<?php

namespace Cerbero\JsonParser\Exceptions;

use Cerbero\JsonParser\Decoders\DecodedValue;
use Exception;

/**
 * The exception thrown when a JSON value cannot be decoded.
 *
 */
final class DecodingException extends Exception implements JsonParserException
{
    /**
     * Instantiate the class
     *
     * @param DecodedValue $decoded
     */
    public function __construct(public readonly DecodedValue $decoded)
    {
        parent::__construct('Decoding error: ' . $decoded->error, (int) $decoded->code);
    }
}
