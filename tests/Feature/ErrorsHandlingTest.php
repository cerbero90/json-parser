<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Exceptions\DecodingException;
use Cerbero\JsonParser\Exceptions\SyntaxException;
use Cerbero\JsonParser\JsonParser;


it('throws a syntax exception on unexpected tokens', function (string $json, string $unexpected, int $position) {
    expect(fn () => JsonParser::parse($json)->traverse())
        ->toThrow(SyntaxException::class, "Syntax error: unexpected '$unexpected' at position {$position}");
})->with(Dataset::forSyntaxErrors());

it('lets the user handle syntax errors', function () {
    JsonParser::parse('{a}')
        ->onSyntaxError(function (SyntaxException $e) {
            expect($e)
                ->getMessage()->toBe("Syntax error: unexpected 'a' at position 2")
                ->value->toBe('a')
                ->position->toBe(2);
        })
        ->traverse();
});

it('throws a decoding exception if unable to decode a JSON fragment', function () {
    JsonParser::parse(fixture('errors/decoding.json'))->traverse();
})->throws(DecodingException::class, 'Decoding error: Syntax error');

it('lets the user handle decoding errors', function () {
    $decodingErrors = [];

    JsonParser::parse(fixture('errors/decoding.json'))
        ->onDecodingError(function (DecodedValue $decoded) use (&$decodingErrors) {
            $decodingErrors[] = $decoded->json;
        })
        ->traverse();

    expect($decodingErrors)->toBe(['1a', '""b', '3.c14', '[f]']);
});

it('lets the user patch decoding errors', function (string $json, mixed $patch, array $patched) {
    expect(JsonParser::parse($json)->patchDecodingError($patch))->toParseTo($patched);
})->with(Dataset::forDecodingErrorsPatching());
