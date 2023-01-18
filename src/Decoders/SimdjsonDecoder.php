<?php

namespace Cerbero\JsonParser\Decoders;

/**
 * The simdjson decoder.
 *
 */
class SimdjsonDecoder extends AbstractDecoder
{
    /**
     * Instantiate the class.
     *
     * @param bool $decodesToArray
     */
    public function __construct(protected bool $decodesToArray = true)
    {
    }

    /**
     * Retrieve the decoded value of the given JSON
     *
     * @param string $json
     * @return mixed
     */
    protected function decodeJson(string $json): mixed
    {
        return simdjson_decode($json, $this->decodesToArray);
    }
}
