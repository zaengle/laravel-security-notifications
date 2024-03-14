<?php

namespace Zaengle\LaravelSecurityNotifications\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Zaengle\LaravelSecurityNotifications\Providers\PackageServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PackageServiceProvider::class,
        ];
    }
}
