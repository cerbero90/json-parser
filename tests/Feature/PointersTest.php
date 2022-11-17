<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\JsonParser;

it('supports single JSON pointers', function (string $json, string $pointer, array $parsed) {
    expect(JsonParser::parse($json)->pointer($pointer))->toParseTo($parsed);
})->with(Dataset::forSinglePointers());


// it('supports multiple JSON pointers', function (string $json, array $pointers, array $parsed) {
//     expect(JsonParser::parse($json)->pointer(...$pointers))->toParseTo($parsed);
// })->with(Dataset::forMultiplePointers());
