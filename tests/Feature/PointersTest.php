<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\Exceptions\IntersectingPointersException;
use Cerbero\JsonParser\Exceptions\InvalidPointerException;
use Cerbero\JsonParser\JsonParser;


it('throws an exception when providing an invalid JSON pointer', function (string $pointer) {
    expect(fn () => JsonParser::parse('{}')->pointer($pointer)->traverse())
        ->toThrow(InvalidPointerException::class, "The string [$pointer] is not a valid JSON pointer");
})->with(Dataset::forInvalidPointers());

it('loads JSON from a single JSON pointer', function (string $json, string $pointer, array $parsed) {
    expect(JsonParser::parse($json)->pointer($pointer))->toPointTo($parsed);
})->with(Dataset::forSinglePointers());

it('eager loads pointers into an array', function (string $json, string $pointer, array $expected) {
    expect(JsonParser::parse($json)->pointer($pointer)->toArray())->toBe($expected);
})->with(Dataset::forSinglePointersToArray());

it('eager loads lazy pointers into an array', function (string $json, string $pointer, array $expected) {
    expect(JsonParser::parse($json)->lazyPointer($pointer)->toArray())->toBe($expected);
})->with(Dataset::forSinglePointersToArray());

it('loads JSON from multiple JSON pointers', function (string $json, array $pointers, array $parsed) {
    expect(JsonParser::parse($json)->pointers($pointers))->toPointTo($parsed);
})->with(Dataset::forMultiplePointers());

it('eager loads multiple pointers into an array', function (string $json, array $pointers, array $expected) {
    expect(JsonParser::parse($json)->pointers($pointers)->toArray())->toBe($expected);
})->with(Dataset::forMultiplePointersToArray());

it('eager loads multiple lazy pointers into an array', function (string $json, array $pointers, array $expected) {
    expect(JsonParser::parse($json)->lazyPointers($pointers)->toArray())->toBe($expected);
})->with(Dataset::forMultiplePointersToArray());

it('can intersect pointers with wildcards', function (string $json, array $pointers, array $parsed) {
    expect(JsonParser::parse($json)->pointers($pointers))->toPointTo($parsed);
})->with(Dataset::forIntersectingPointersWithWildcards());

it('throws an exception when two pointers intersect', function (string $json, array $pointers, string $message) {
    expect(fn () => JsonParser::parse($json)->pointers($pointers))
        ->toThrow(IntersectingPointersException::class, $message);
})->with(Dataset::forIntersectingPointers());

it('lazy loads JSON from a single lazy JSON pointer', function (string $json, string $pointer, array $sequence) {
    expect(JsonParser::parse($json)->lazyPointer($pointer))->sequence(...$sequence);
})->with(Dataset::forSingleLazyPointers());

it('lazy loads JSON from multiple lazy JSON pointers', function (string $json, array $pointers, array $sequence) {
    expect(JsonParser::parse($json)->lazyPointers($pointers))->sequence(...$sequence);
})->with(Dataset::forMultipleLazyPointers());

it('lazy loads JSON recursively', function (string $json, string $pointer, array $keys, array $expected) {
    expect(JsonParser::parse($json)->lazyPointer($pointer))->toLazyLoadRecursively($keys, $expected);
})->with(Dataset::forRecursiveLazyLoading());

it('mixes pointers and lazy pointers', function (string $json, array $pointers, array $lazyPointers, array $expected) {
    expect(JsonParser::parse($json)->pointers($pointers)->lazyPointers($lazyPointers))->toParseTo($expected);
})->with(Dataset::forMixedPointers());
