<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Sources\Endpoint;
use Cerbero\JsonParser\Sources\Psr7Request;
use DirectoryIterator;
use Generator;
use Mockery;
use Pest\Expectation;

/**
 * The dataset provider.
 *
 */
final class Dataset
{
    /**
     * Retrieve the dataset to test parsing
     *
     * @return Generator
     */
    public static function forParsing(): Generator
    {
        foreach (self::fixtures() as $fixture) {
            $name = $fixture->getBasename('.json');

            yield [
                file_get_contents($fixture->getRealPath()),
                require fixture("parsing/{$name}.php"),
            ];
        }
    }

    /**
     * Retrieve the fixtures
     *
     * @return Generator<int, DirectoryIterator>
     */
    protected static function fixtures(): Generator
    {
        foreach (new DirectoryIterator(fixture('json')) as $file) {
            if (!$file->isDot()) {
                yield $file;
            }
        }
    }

    /**
     * Retrieve the dataset to test invalid pointers
     *
     * @return Generator
     */
    public static function forInvalidPointers(): Generator
    {
        yield from ['abc', '/foo~2', '/~', ' '];
    }

    /**
     * Retrieve the dataset to test single pointers
     *
     * @return Generator
     */
    public static function forSinglePointers(): Generator
    {
        $singlePointers = require fixture('pointers/single_pointer.php');

        foreach ($singlePointers as $fixture => $pointers) {
            $json = file_get_contents(fixture("json/{$fixture}.json"));

            foreach ($pointers as $pointer => $value) {
                yield [$json, $pointer, $value];
            }
        }
    }

    /**
     * Retrieve the dataset to test multiple pointers
     *
     * @return Generator
     */
    public static function forMultiplePointers(): Generator
    {
        $multiplePointers = require fixture('pointers/multiple_pointers.php');

        foreach ($multiplePointers as $fixture => $valueByPointers) {
            $json = file_get_contents(fixture("json/{$fixture}.json"));

            foreach ($valueByPointers as $pointers => $value) {
                yield [$json, explode(',', $pointers), $value];
            }
        }
    }

    /**
     * Retrieve the dataset to test intersecting pointers with wildcards
     *
     * @return Generator
     */
    public static function forIntersectingPointersWithWildcards(): Generator
    {
        $json = fixture('json/complex_object.json');

        $pointers = [
            '/topping/6/type' => fn (string $value) => "$value @ /topping/6/type",
            '/topping/-/type' => fn (string $value) => "$value @ /topping/-/type",
            '/topping/0/type' => fn (string $value) => "$value @ /topping/0/type",
            '/topping/2/type' => fn (string $value) => "$value @ /topping/2/type",
        ];

        $parsed = [
            'type' => [
                'None @ /topping/0/type',
                'Glazed @ /topping/-/type',
                'Sugar @ /topping/2/type',
                'Powdered Sugar @ /topping/-/type',
                'Chocolate with Sprinkles @ /topping/-/type',
                'Chocolate @ /topping/-/type',
                'Maple @ /topping/6/type',
            ]
        ];

        yield [$json, $pointers, $parsed];
    }

    /**
     * Retrieve the dataset to test intersecting pointers
     *
     * @return Generator
     */
    public static function forIntersectingPointers(): Generator
    {
        $json = fixture('json/complex_object.json');
        $message = 'The pointers [%s] and [%s] are intersecting';
        $pointersByIntersection = [
            '/topping,/topping/0' => [
                '/topping',
                '/topping/0',
            ],
            '/topping/0,/topping' => [
                '/topping/0',
                '/topping',
            ],
            '/topping,/topping/-' => [
                '/topping',
                '/topping/-',
            ],
            '/topping/-,/topping' => [
                '/topping/-',
                '/topping',
            ],
            '/topping/0/type,/topping' => [
                '/topping/0/type',
                '/topping/-/type',
                '/topping',
            ],
            '/topping,/topping/-/type' => [
                '/topping',
                '/topping/-/type',
                '/topping/0/type',
            ],
            '/topping/-/type,/topping/-/type/baz' => [
                '/topping/-/type',
                '/topping/-/types',
                '/topping/-/type/baz',
            ],
            '/topping/-/type/baz,/topping/-/type' => [
                '/topping/-/type/baz',
                '/topping/-/type',
                '/topping/-/types',
            ],
        ];

        foreach ($pointersByIntersection as $intersection => $pointers) {
            yield [$json, $pointers, vsprintf($message, explode(',', $intersection))];
        }
    }

    /**
     * Retrieve the dataset to test single lazy pointers
     *
     * @return Generator
     */
    public static function forSingleLazyPointers(): Generator
    {
        $json = fixture('json/complex_object.json');
        $sequenceByPointer = [
            '' => [
                fn ($value, $key) => $key->toBe('id')->and($value->value)->toBe('0001'),
                fn ($value, $key) => $key->toBe('type')->and($value->value)->toBe('donut'),
                fn ($value, $key) => $key->toBe('name')->and($value->value)->toBe('Cake'),
                fn ($value, $key) => $key->toBe('ppu')->and($value->value)->toBe(0.55),
                fn ($value, $key) => $key->toBe('batters')->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe('topping')->and($value->value)->toBeInstanceOf(Parser::class),
            ],
            '/batters/batter/-' => [
                fn ($value, $key) => $key->toBe(0)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(1)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(2)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(3)->and($value->value)->toBeInstanceOf(Parser::class),
            ],
            '/topping/-' => [
                fn ($value, $key) => $key->toBe(0)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(1)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(2)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(3)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(4)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(5)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(6)->and($value->value)->toBeInstanceOf(Parser::class),
            ],
        ];

        foreach ($sequenceByPointer as $pointer => $sequence) {
            yield [$json, $pointer, $sequence];
        }
    }

    /**
     * Retrieve the dataset to test multiple lazy pointers
     *
     * @return Generator
     */
    public static function forMultipleLazyPointers(): Generator
    {
        $json = fixture('json/complex_object.json');
        $sequenceByPointer = [
            '/topping,/batters' => [
                fn ($value, $key) => $key->toBe('batters')->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe('topping')->and($value->value)->toBeInstanceOf(Parser::class),
            ],
            '/topping/-,/batters/batter' => [
                fn ($value, $key) => $key->toBe('batter')->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(0)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(1)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(2)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(3)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(4)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(5)->and($value->value)->toBeInstanceOf(Parser::class),
                fn ($value, $key) => $key->toBe(6)->and($value->value)->toBeInstanceOf(Parser::class),
            ],
        ];

        foreach ($sequenceByPointer as $pointers => $sequence) {
            yield [$json, explode(',', $pointers), $sequence];
        }
    }

    /**
     * Retrieve the dataset to test recursive lazy loading
     *
     * @return Generator
     */
    public static function forRecursiveLazyLoading(): Generator
    {
        $json = fixture('json/complex_object.json');
        $expectedByKeys = [
            'batters,batter' => [
                ['id' => '1001', 'type' => 'Regular'],
                ['id' => '1002', 'type' => 'Chocolate'],
                ['id' => '1003', 'type' => 'Blueberry'],
                ['id' => '1004', 'type' => 'Devil\'s Food'],
            ],
            'topping' => [
                ['id' => '5001', 'type' => 'None'],
                ['id' => '5002', 'type' => 'Glazed'],
                ['id' => '5005', 'type' => 'Sugar'],
                ['id' => '5007', 'type' => 'Powdered Sugar'],
                ['id' => '5006', 'type' => 'Chocolate with Sprinkles'],
                ['id' => '5003', 'type' => 'Chocolate'],
                ['id' => '5004', 'type' => 'Maple'],
            ],
        ];

        foreach ($expectedByKeys as $keys => $expected) {
            $keys = explode(',', $keys);
            yield [$json, '/' . $keys[0], $keys, $expected];
        }
    }

    /**
     * Retrieve the dataset to test syntax errors
     *
     * @return Generator
     */
    public static function forSyntaxErrors(): Generator
    {
        yield from require fixture('errors/syntax.php');
    }

    /**
     * Retrieve the dataset to test decoding errors patching
     *
     * @return Generator
     */
    public static function forDecodingErrorsPatching(): Generator
    {
        $patches = [null, 'baz', 123];
        $json = '[1a, ""b, "foo", 3.1c4, falsed, null, [1, 2e], {"bar": 1, "baz"f: 2}]';
        $patchJson = fn (mixed $patch) => [$patch, $patch, 'foo', $patch, $patch, null, $patch, $patch];

        foreach ($patches as $patch) {
            yield [$json, $patch, $patchJson($patch)];
        }

        $patch = fn (DecodedValue $decoded) => strrev($decoded->json);
        $patched = ['a1', 'b""', 'foo', '4c1.3', 'deslaf', null, ']e2,1[', '}2:f"zab",1:"rab"{'];

        yield [$json, fn () => $patch, $patched];
    }

    /**
     * Retrieve the dataset to test sources requiring Guzzle
     *
     * @return Generator
     */
    public static function forSourcesRequiringGuzzle(): Generator
    {
        $sources = [Endpoint::class, Psr7Request::class];

        foreach ($sources as $source) {
            yield Mockery::mock($source)
                ->makePartial()
                ->shouldAllowMockingProtectedMethods()
                ->shouldReceive('guzzleIsInstalled')
                ->andReturn(false)
                ->getMock();
        }
    }

    /**
     * Retrieve the dataset to test decoders
     *
     * @return Generator
     */
    public static function forDecoders(): Generator
    {
        $json = '{"foo":"bar"}';
        $values = [
            true => ['foo' => 'bar'],
            false => (object) ['foo' => 'bar'],
        ];

        foreach ([true, false] as $decodesToArray) {
            yield [$decodesToArray, $json, $values[$decodesToArray]];
        }
    }
}
