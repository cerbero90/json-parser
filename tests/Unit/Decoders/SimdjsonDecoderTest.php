<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Decoders\SimdjsonDecoder;


it('decodes values when a JSON is valid', function (bool $decodesToArray, string $json, mixed $value) {
    expect($decoded = (new SimdjsonDecoder($decodesToArray))->decode($json))
        ->toBeInstanceOf(DecodedValue::class)
        ->succeeded->toBeTrue()
        ->value->toEqual($value)
        ->error->toBeNull()
        ->code->toBeNull()
        ->exception->toBeNull();

    expect($decoded->json)->toBeNull();
})->with(Dataset::forDecoders());

it('reports issues when a JSON is not valid', function () {
    $json = '[1a]';
    $e = new SimdJsonException('Problem while parsing a number', 9);

    expect($decoded = (new SimdjsonDecoder())->decode($json))
        ->toBeInstanceOf(DecodedValue::class)
        ->succeeded->toBeFalse()
        ->value->toBeNull()
        ->error->toBe($e->getMessage())
        ->code->toBe($e->getCode())
        ->exception->toEqual($e);

    expect($decoded->json)->toBe($json);
});
