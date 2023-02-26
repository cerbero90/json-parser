<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\DecodedValue;
use Cerbero\JsonParser\Sources\Endpoint;
use Cerbero\JsonParser\Sources\Psr7Request;
use DirectoryIterator;
use Generator;
use Mockery;

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
        foreach (static::fixtures() as $fixture) {
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
