<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\Exceptions\GuzzleRequiredException;
use Cerbero\JsonParser\Exceptions\UnsupportedSourceException;
use Cerbero\JsonParser\JsonParser;
use Cerbero\JsonParser\Sources\Source;


it('throws an exception when a JSON source is not supported', function () {
    expect(fn () => JsonParser::parse(123)->traverse())
        ->toThrow(UnsupportedSourceException::class, 'Unable to load JSON from the provided source');
});

it('throws an exception when Guzzle is required but not installed', function (Source $source) {
    expect(fn () => JsonParser::parse($source)->traverse())
        ->toThrow(GuzzleRequiredException::class, 'Guzzle is required to load JSON from endpoints');
})->with(Dataset::forSourcesRequiringGuzzle());

it('supports multiple sources', function (Source $source, int $size) {
    expect($source)
        ->getIterator()->toBeInstanceOf(Traversable::class)
        ->matches()->toBeTrue()
        ->size()->toBe($size);
})->with(Dataset::forSources());
