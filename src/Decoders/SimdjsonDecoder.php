<?php

namespace Cerbero\JsonParser\Decoders;

/**
 * The decoder using the simdjson library.
 *
 */
final class SimdjsonDecoder extends AbstractDecoder
{
    /**
     * Instantiate the class.
     *
     * @param bool $decodesToArray
     */
    public function __construct(private bool $decodesToArray = true)
    {
    }

    /**
     * Retrieve the decoded value of the given JSON
     *
     * @param string $json
     * @return mixed
     * @throws \Throwable
     */
    protected function decodeJson(string $json): mixed
    {
        return simdjson_decode($json, $this->decodesToArray);
    }
}
