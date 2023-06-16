<?php

namespace Cerbero\JsonParser\Decoders;

use Throwable;

/**
 * The abstract implementation of a JSON decoder.
 *
 */
abstract class AbstractDecoder implements Decoder
{
    /**
     * Retrieve the decoded value of the given JSON
     *
     * @param string $json
     * @return mixed
     * @throws Throwable
     */
    abstract protected function decodeJson(string $json): mixed;

    /**
     * Decode the given JSON.
     *
     * @param string $json
     * @return DecodedValue
     */
    public function decode(string $json): DecodedValue
    {
        try {
            $value = $this->decodeJson($json);
        } catch (Throwable $e) {
            return DecodedValue::failed($e, $json);
        }

        return DecodedValue::succeeded($value);
    }
}
