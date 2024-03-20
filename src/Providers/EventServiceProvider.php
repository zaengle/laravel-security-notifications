<?php

namespace Zaengle\LaravelSecurityNotifications\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;
use Zaengle\LaravelSecurityNotifications\Listeners\SendSecurityNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SecureFieldsUpdated::class => [
            SendSecurityNotification::class,
        ],
    ];
}
