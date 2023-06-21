<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Decoders\JsonDecoder;


it('decodes values when a JSON is valid', function (bool $decodesToArray, string $json, mixed $value) {
    expect($decoded = (new JsonDecoder($decodesToArray))->decode($json))
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
    $e = new JsonException('Syntax error', 4);

    expect($decoded = (new JsonDecoder())->decode($json))
        ->toBeInstanceOf(DecodedValue::class)
        ->succeeded->toBeFalse()
        ->value->toBeNull()
        ->error->toBe($e->getMessage())
        ->code->toBe($e->getCode())
        ->exception->toEqual($e);

    expect($decoded->json)->toBe($json);
});
