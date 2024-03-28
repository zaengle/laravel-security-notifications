<?php

namespace Zaengle\LaravelSecurityNotifications\Providers;

use Illuminate\Support\ServiceProvider;
use Zaengle\LaravelSecurityNotifications\IPAddressHandler;

class PackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('security-notifications.php'),
            ], 'config');
        }

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'security-notifications');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/security-notifications'),
            ], 'views');
        }

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateLoginsTable')) {
                $this->publishes([
                    __DIR__ . '/../../database/migrations/create_logins_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_logins_table.php'),
                ], 'migrations');
            }
        }
    }

    public function register(): void
    {
        parent::register();

        $this->app->register(EventServiceProvider::class);

        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'security-notifications');

        $this->app->singleton('ip-address', fn () => new IPAddressHandler);
    }
}
