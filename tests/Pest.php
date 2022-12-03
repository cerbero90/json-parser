<?php

/**
 * Expect that keys and values are parsed correctly
 *
 * @param array $expected
 * @return Expectation
 */
expect()->extend('toParseTo', function (array $expected) {
    $actual = [];

    foreach ($this->value as $parsedKey => $parsedValue) {
        expect($parsedValue)->toBe($expected[$parsedKey]);

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

        // the following match is required as we may deal with parsed values that are arrays
        // and unpacking a parsed value that is an array may lead to unexpected results
        $actual[$parsedKey] = match ($itemsCount[$parsedKey]) {
            1 => $parsedValue,
            2 => [$actual[$parsedKey], $parsedValue],
            default => [...$actual[$parsedKey], $parsedValue],
        };
    }

    return expect($actual)->toBe($expected);
});
