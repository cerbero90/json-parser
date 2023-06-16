<?php

namespace Cerbero\JsonParser\Decoders;

/**
 * The decoder using the built-in JSON decoder.
 *
 */
final class JsonDecoder extends AbstractDecoder
{
    /**
     * Instantiate the class.
     *
     * @param bool $decodesToArray
     * @param int<1, max> $depth
     */
    public function __construct(private readonly bool $decodesToArray = true, private readonly int $depth = 512)
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
        return json_decode($json, $this->decodesToArray, $this->depth, JSON_THROW_ON_ERROR);
    }
}
