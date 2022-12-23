<?php

use Cerbero\JsonParser\Dataset;
use Cerbero\JsonParser\JsonParser;

use function Cerbero\JsonParser\parseJson;

it('parses JSON when instantiated', function (string $json, array $parsed) {
    expect(new JsonParser($json))->toParseTo($parsed);
})->with(Dataset::forParsing());

it('parses JSON when calling the factory method', function (string $json, array $parsed) {
    expect(JsonParser::parse($json))->toParseTo($parsed);
})->with(Dataset::forParsing());

it('parses JSON when calling the helper', function (string $json, array $parsed) {
    expect(parseJson($json))->toParseTo($parsed);
})->with(Dataset::forParsing());
