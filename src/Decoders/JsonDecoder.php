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
        return json_decode($json, $this->decodesToArray, flags: JSON_THROW_ON_ERROR);
    }
}
