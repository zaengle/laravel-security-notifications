<?php

namespace Zaengle\LaravelSecurityNotifications\Tests\Setup\Services;

use Zaengle\LaravelSecurityNotifications\Models\Login;
use Zaengle\LaravelSecurityNotifications\Services\DigestIPAddress;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;

readonly class CustomIPAddressDriver implements DigestIPAddress
{
    public function __construct(private readonly string $ipAddress)
    {
    }

    public function handle(): void
    {
        Login::create([
            'ip_address' => $this->ipAddress,
            'user_id' => 1,
            'user_type' => User::class,
            'first_login_at' => now(),
            'last_login_at' => now(),
            'location_data' => [
                'custom' => 'driver',
            ],
        ]);
    }
}