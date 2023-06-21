<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\Decoders\SimdjsonDecoder;
use Cerbero\JsonParser\JsonParser;

use function Cerbero\JsonParser\parseJson;

it('parses JSON when instantiated', function (string $json, array $parsed) {
    expect(new JsonParser($json))->toParseTo($parsed);
})->with(Dataset::forParsing());

it('parses JSON when instantiated statically', function (string $json, array $parsed) {
    expect(JsonParser::parse($json))->toParseTo($parsed);
})->with(Dataset::forParsing());

it('parses JSON when calling the helper', function (string $json, array $parsed) {
    expect(parseJson($json))->toParseTo($parsed);
})->with(Dataset::forParsing());

it('parses with custom decoders', function (string $json, array $parsed) {
    expect(JsonParser::parse($json)->decoder(new SimdjsonDecoder()))->toParseTo($parsed);
})->with(Dataset::forParsing());

it('parses a custom number of bytes', function (string $json, array $parsed) {
    expect(JsonParser::parse($json)->bytes(1024))->toParseTo($parsed);
})->with(Dataset::forParsing());

it('eager loads JSON into an array', function (string $json, array $parsed) {
    expect(JsonParser::parse($json)->toArray())->toBe($parsed);
})->with(Dataset::forParsing());

it('shows the progress while parsing', function () {
    $parser = new JsonParser(fixture('json/simple_array.json'));

    expect($parser->progress()->percentage())->toBe($percentage = 0.0);

    foreach ($parser as $value) {
        expect($percentage)->toBeLessThan($percentage = $parser->progress()->percentage());
    }

    expect($parser->progress()->percentage())->toBe(100.0);
});

// it('accesses nodes like an array', function () {
//     $source = 'https://randomuser.me/api/1.4?seed=json-parser&results=5';
//     $json = JsonParser::parse($source)->pointer('/results/0/location');
//     dd($json['location']['city'], $json['location']);
//     dd($json[0], $json[1]);
//     dd($json[0]['gender'], $json[0]['name'], $json[0]['email']);
// })->only();
