<?php

use Cerbero\JsonParser\JsonParser;

if (!function_exists('parseJson')) {
    /**
     * Parse the given source of JSON
     *
     * @param mixed $source
     * @return iterable
     */
    function parseJson(mixed $source): iterable
    {
        return new JsonParser($source);
    }
}
