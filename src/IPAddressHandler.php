<?php

namespace Zaengle\LaravelSecurityNotifications;

use Zaengle\LaravelSecurityNotifications\Jobs\ProcessNewIPAddress;
use Zaengle\LaravelSecurityNotifications\Models\Login;

readonly class IPAddressHandler
{
    public function process(string $ipAddress): void
    {
        if (
            $login = Login::query()
                ->where('ip_address', $ipAddress)
                ->where('user_id', auth()->id())
                ->first()
        ) {
            $login->update(['last_login_at' => now()]);
        } else {
            ProcessNewIPAddress::dispatch($ipAddress);
        }
    }
}