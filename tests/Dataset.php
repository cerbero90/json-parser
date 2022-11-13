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
}
