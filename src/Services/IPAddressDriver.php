<?php

namespace Zaengle\LaravelSecurityNotifications\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
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
        $ipLocationData = Http::get('http://ip-api.com/json/'.$this->ipAddress)->json();

        $loginQuery = Login::query()
            ->where([
                'user_id' => $this->userId,
                'user_type' => $this->userType,
            ]);

        if (config('security-notifications.allow_same_location_login')) {
            $loginQuery = $loginQuery->where(function ($query) use ($ipLocationData) {
                $query->where('ip_address', $this->ipAddress)
                    ->orWhere(function ($query) use ($ipLocationData) {
                        $query->where([
                            'location_data->city' => Arr::get($ipLocationData, 'city'),
                            'location_data->region' => Arr::get($ipLocationData, 'region'),
                        ]);
                    });
            });
        } else {
            $loginQuery = $loginQuery->where('ip_address', $this->ipAddress);
        }

        if ($login = $loginQuery->first()) {
            $login->update([
                'ip_address' => $this->ipAddress,
                'last_login_at' => now(),
            ]);
        } else {
            ProcessNewIPAddress::dispatch(
                ipLocationData: $ipLocationData,
                userId: $this->userId,
                userType: $this->userType,
            );
        }
    }
}