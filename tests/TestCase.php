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

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__ . '/../database/migrations/create_users_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_logins_table.php.stub';

        (new \CreateUsersTable)->up();
        (new \CreateLoginsTable)->up();
    }
}
