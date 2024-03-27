<?php

namespace Zaengle\LaravelSecurityNotifications\Services;

use Zaengle\LaravelSecurityNotifications\Jobs\ProcessNewIPAddress;
use Zaengle\LaravelSecurityNotifications\Models\Login;

readonly class IPAddressDriver implements DigestIPAddress
{
    public function __construct(
        private readonly string $ipAddress,
        private readonly int $userId,
        private readonly string $userType,
    )
    {
    }

    public function handle(): void
    {
        if (
            $login = Login::query()
                ->where([
                    'ip_address' => $this->ipAddress,
                    'user_id' => $this->userId,
                    'user_type' => $this->userType,
                ])
                ->first()
        ) {
            $login->update(['last_login_at' => now()]);
        } else {
            ProcessNewIPAddress::dispatch(
                ipAddress: $this->ipAddress,
                userId: $this->userId,
                userType: $this->userType,
            );
        }
    }
}