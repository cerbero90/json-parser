<?php

namespace Cerbero\JsonParser;

/**
 * Parse the given source of JSON
 *
 * @param mixed $source
 * @return JsonParser
 */
function parseJson(mixed $source): JsonParser
{
    return new JsonParser($source);
}
