<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\Exceptions\InvalidPointerException;
use Cerbero\JsonParser\JsonParser;


it('throws an exception when providing an invalid JSON pointer', function (string $pointer) {
    expect(fn () => JsonParser::parse('{}')->pointer($pointer)->traverse())
        ->toThrow(InvalidPointerException::class, "The string [$pointer] is not a valid JSON pointer");
})->with(Dataset::forInvalidPointers());

it('supports single JSON pointers', function (string $json, string $pointer, array $parsed) {
    expect(JsonParser::parse($json)->pointer($pointer))->toPointTo($parsed);
})->with(Dataset::forSinglePointers());

it('supports multiple JSON pointers', function (string $json, array $pointers, array $parsed) {
    expect(JsonParser::parse($json)->pointers($pointers))->toPointTo($parsed);
})->with(Dataset::forMultiplePointers());
