<?php

namespace Cerbero\JsonParser\Decoders;

/**
 * The decoder using the simdjson extension.
 *
 */
final class SimdjsonDecoder extends AbstractDecoder
{
    /**
     * Instantiate the class.
     *
     * @param bool $decodesToArray
     * @param int $depth
     */
    public function __construct(private bool $decodesToArray = true, private int $depth = 512)
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
        return simdjson_decode($json, $this->decodesToArray, $this->depth);
    }
}
