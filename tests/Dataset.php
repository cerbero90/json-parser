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
        foreach (static::fixtureNamesIn('parsing') as $fixture) {
            yield [
                file_get_contents(__DIR__ . "/fixtures/parsing/{$fixture}.json"),
                require __DIR__ . "/fixtures/parsing/{$fixture}.php",
            ];
        }
    }

    /**
     * Retrieve the names of the fixtures
     *
     * @param string $directory
     * @return Generator
     */
    protected static function fixtureNamesIn(string $directory): Generator
    {
        $fixtures = new DirectoryIterator(__DIR__ . "/fixtures/{$directory}");

        foreach ($fixtures as $file) {
            if ($file->getExtension() === 'json') {
                yield $file->getBasename('.json');
            }
        }
    }
}
