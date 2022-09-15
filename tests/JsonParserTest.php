<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Providers\JsonParserServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * The package test suite.
 *
 */
class JsonParserTest extends TestCase
{
    /**
     * Retrieve the package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            JsonParserServiceProvider::class,
        ];
    }
}
