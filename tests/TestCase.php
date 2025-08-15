<?php

declare(strict_types=1);

namespace LaravelQueryKit\Tests;

use LaravelQueryKit\Providers\QueryKitServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            QueryKitServiceProvider::class,
        ];
    }
}
