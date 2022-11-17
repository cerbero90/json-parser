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
                require __DIR__ . "/fixtures/parsing/{$name}.php",
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
        foreach (new DirectoryIterator(__DIR__ . '/fixtures/json') as $file) {
            if (!$file->isDot()) {
                yield $file;
            }
        }
    }

    /**
     * Retrieve the dataset to test single pointers
     *
     * @return Generator
     */
    public static function forSinglePointers(): Generator
    {
        $singlePointers = require __DIR__ . '/fixtures/pointers/single_pointer.php';

        foreach ($singlePointers as $fixture => $pointers) {
            $json = file_get_contents(__DIR__ . "/fixtures/json/{$fixture}.json");

            foreach ($pointers as $pointer => $value) {
                yield [$json, $pointer, $value];
            }
        }
    }
}
