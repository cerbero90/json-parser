<?php

namespace Cerbero\JsonParser;

use DirectoryIterator;
use Generator;

/**
 * The datasets entry-point.
 *
 */
class Dataset
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
}
