<?php

namespace Cerbero\JsonParser\Decoders;

/**
 * The JSON decoder interface.
 *
 */
interface Decoder
{
    /**
     * Decode the given JSON.
     *
     * @param string $json
     * @return DecodedValue
     */
    public function decode(string $json): DecodedValue;
}
