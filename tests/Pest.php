<?php

use Cerbero\JsonParser\Tokens\Parser;
use Pest\Expectation;

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
 * Expect the given sequence from a Traversable
 * Temporary fix to sequence() until this PR is merged: https://github.com/pestphp/pest/pull/895
 *
 * @param mixed ...$callbacks
 * @return Expectation
 */
expect()->extend('traverse', function (mixed ...$callbacks) {
    if (! is_iterable($this->value)) {
        throw new BadMethodCallException('Expectation value is not iterable.');
    }

    if (empty($callbacks)) {
        throw new InvalidArgumentException('No sequence expectations defined.');
    }

    $index = $valuesCount = 0;

    foreach ($this->value as $key => $value) {
        $valuesCount++;

        if ($callbacks[$index] instanceof Closure) {
            $callbacks[$index](new self($value), new self($key));
        } else {
            (new self($value))->toEqual($callbacks[$index]);
        }

        $index = isset($callbacks[$index + 1]) ? $index + 1 : 0;
    }

    if (count($callbacks) > $valuesCount) {
        throw new OutOfRangeException('Sequence expectations are more than the iterable items');
    }

    return $this;
});

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
 * @param array $keys
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

    return $this;
});

/**
 * Expect that all Parser instances are wrapped recursively
 *
 * @param string $wrapper
 * @return Expectation
 */
expect()->extend('toBeWrappedInto', function (string $wrapper) {
    return $this->when(is_object($this->value), fn (Expectation $value) => $value
        ->toBeInstanceOf($wrapper)
        ->not->toBeInstanceOf(Parser::class)
        ->traverse(fn (Expectation $value) => $value->toBeWrappedInto($wrapper))
    );
});
