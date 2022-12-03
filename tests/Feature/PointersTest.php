<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\Exceptions\PointerException;
use Cerbero\JsonParser\JsonParser;


it('supports single JSON pointers', function (string $json, string $pointer, array $parsed) {
    expect(JsonParser::parse($json)->pointer($pointer))->toPointTo($parsed);
})->with(Dataset::forSinglePointers());

it('throws an exception when providing an invalid JSON pointer', function (string $pointer) {
    expect(fn () => iterator_to_array(JsonParser::parse('{}')->pointer($pointer)))
        ->toThrow(PointerException::class, "The string [$pointer] is not a valid JSON pointer");
})->with(Dataset::forInvalidPointers());

// it('supports multiple JSON pointers', function (string $json, array $pointers, array $parsed) {
//     expect(JsonParser::parse($json)->pointer(...$pointers))->toParseTo($parsed);
// })->with(Dataset::forMultiplePointers());
