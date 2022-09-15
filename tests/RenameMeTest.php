<?php

namespace Cerbero\:package_ns;

use Cerbero\:package_ns\Providers\:package_nsServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * The package test suite.
 *
 */
class :package_nsTest extends TestCase
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
            :package_nsServiceProvider::class,
        ];
    }
}
