<?php

use Cerbero\JsonParser\Parser;

if (!function_exists('fixture')) {
    /**
     * Retrieve the absolute path of the given fixture
     *
     * @param string $fixture
     * @return string
     */
    function fixture(string $fixture): string
    {
        return __DIR__ . "/fixtures/{$fixture}";
    }
}

/**
 * Expect that keys and values are parsed correctly
 *
 * @param array $expected
 * @return Expectation
 */
expect()->extend('toParseTo', function (array $expected) {
    $actual = [];

    foreach ($this->value as $parsedKey => $parsedValue) {
        expect($expected)->toHaveKey($parsedKey, $parsedValue);

        $actual[$parsedKey] = $parsedValue;
    }

    return expect($actual)->toBe($expected);
});

/**
 * Expect that values defined by JSON pointers are parsed correctly
 *
 * @param array $expected
 * @return Expectation
 */
expect()->extend('toPointTo', function (array $expected) {
    $actual = $itemsCount = [];

    foreach ($this->value as $parsedKey => $parsedValue) {
        $itemsCount[$parsedKey] = empty($itemsCount[$parsedKey]) ? 1 : $itemsCount[$parsedKey] + 1;

        // associate $parsedKey to $parsedValue if $parsedKey occurs once
        // associate $parsedKey to an array of $parsedValue if $parsedKey occurs multiple times
        $actual[$parsedKey] = match ($itemsCount[$parsedKey]) {
            1 => $parsedValue,
            2 => [$actual[$parsedKey], $parsedValue],
            default => [...$actual[$parsedKey], $parsedValue],
        };
    }

    return expect($actual)->toBe($expected);
});

/**
 * Expect that values defined by lazy JSON pointers are parsed correctly
 *
 * @param array $expected
 * @return Expectation
 */
expect()->extend('toLazyLoadRecursively', function (array $keys, array $expected) {
    foreach ($this->value as $key => $value) {
        expect($value)->toBeInstanceOf(Parser::class);

        if (is_null($expectedKey = array_shift($keys))) {
            expect($key)->toBeInt()->and($value)->toParseTo($expected[$key]);
        } else {
            expect($key)->toBe($expectedKey)->and($value)->toLazyLoadRecursively($keys, $expected);
        }
    }
});
