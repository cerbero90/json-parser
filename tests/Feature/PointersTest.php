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

it('can modify key and value of a pointer', function (string $json, array $pointers, array $expected) {
    expect(JsonParser::parse($json)->pointers($pointers)->toArray())->toBe($expected);
})->with(Dataset::forKeyUpdate());

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


it('ciao', function () {
    $json = JsonParser::parse(fixture('json/complex_object.json'))->lazyPointer('');
    $batters = $json->batters;
    $batter = $batters->batter;
    dd(iterator_to_array($batter));
    dd($batter->toArray());
    // this works correctly: to access the same node multiple times, assign it to a variable
    // $firstBatter = $batter[0];
    // dd($firstBatter->id, $firstBatter->type);
    // // this doesn't work: cannot access the same node twice (expected)
    // dd($batter[0]->id, $batter[0]->type);
    // this doesn't work: $batter[1] is actually $batter[0]->type (not expected)
    dd($batter[0]->id, $batter[1]->id);
    // this doesn't work: $batter[2]->id is actually $batter[1]->id (not expected)
    dd($batter[0]->id, $batter[2]->id);


    dd($batter[0]->id, $batter[1]->id);
    dd($json->batters->toArray(), $json->topping->toArray());
    // dd($json->batters->toArray());
    foreach ($json as $key => $value) {
        dump("$key => " . (is_object($value) ? $value::class : $value));
    }
    dd();
    // dd($json->id, $json->type);
    // dd($json->ppu, $json->batters);
    // dd($json->batters, $json->batters);
    dd($json->batters->toArray(), $json->topping->toArray());
    dd($json->batters['batter'][0]['id'], $json->batters['batter'][0]['type']);
    dd($json->batters->batter[0]->id, $json->batters->batter[0]->type);
    $batters = $json->batters;
    dd($batters->batter);
    // it's working!
    // $json = JsonParser::parse(fixture('json/complex_object.json'));
    // dd($json->type, $json['name'], $json->topping);

    $json = JsonParser::parse(fixture('json/complex_object.json'))->pointers(['/batters/batter', '/topping']);
    dd($json->batter, $json['topping']);

    // this cannot work as we are trying to access the same key ("results") twice -> write in the docs
    // $json = JsonParser::parse('https://randomuser.me/api/1.4?seed=json-parser&results=5');
    // dd($json['results'][0]['gender'], $json['results'][0]['email']);
})->only();
