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
    public function __construct(public DecodedValue $decoded)
    {
        parent::__construct('Decoding error: ' . $decoded->error, $decoded->code);
    }
}
