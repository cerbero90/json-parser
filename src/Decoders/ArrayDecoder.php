<?php

namespace Cerbero\JsonParser\Decoders;

use JsonException;

/**
 * The decoder to turn a JSON into an associative array.
 *
 */
class ArrayDecoder implements Decoder
{
    /**
     * Whether to decode the JSON into an associative array.
     *
     * @var bool
     */
    protected bool $decodesToArray = true;

    /**
     * Decode the given JSON.
     *
     * @param string $json
     * @return DecodedValue
     */
    public function decode(string $json): DecodedValue
    {
        try {
            $value = json_decode($json, $this->decodesToArray, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return DecodedValue::failed($e, $json);
        }

        return DecodedValue::succeeded($value);
    }
}
