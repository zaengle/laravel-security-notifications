<?php

namespace Zaengle\LaravelSecurityNotifications\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static process()
 */
class IPAddress extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ip-address';
    }
}