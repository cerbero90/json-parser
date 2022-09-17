<?php

namespace Cerbero\JsonParser\Decoders;

/**
 * The decoder to turn a JSON into an object.
 *
 */
class ObjectDecoder extends ArrayDecoder
{
    /**
     * Whether to decode the JSON into an associative array.
     *
     * @var bool
     */
    protected bool $decodesToArray = false;
}
