<?php

namespace Zaengle\LaravelSecurityNotifications\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Zaengle\LaravelSecurityNotifications\Jobs\ProcessNewIPAddress;
use Zaengle\LaravelSecurityNotifications\Models\Login;

readonly class IPAddressDriver implements DigestIPAddress
{
    public function __construct(
        private string $ipAddress,
        private int $userId,
        private string $userType,
        private bool $sendNewIpNotification = true,
    )
    {
    }

    public function handle(): void
    {
        $endpoint = config('security-notifications.ip-api-key')
            ? 'https://pro.ip-api.com/json/'
            : 'https://ip-api.com/json/';

        $ipLocationData = Http::retry(3)
            ->withQueryParameters(['key' => config('security-notifications.ip-api-key')])
            ->get($endpoint.$this->ipAddress)
            ?->json();

        throw_if(is_null($ipLocationData), new Exception('Failed to get IP location data for: '.$this->ipAddress));

        $loginQuery = Login::query()
            ->where([
                'user_id' => $this->userId,
                'user_type' => $this->userType,
            ]);

        $existenceCheckQuery = clone $loginQuery;

        if (config('security-notifications.allow_same_location_login')) {
            $loginQuery->when(
                $existenceCheckQuery->where('ip_address', $this->ipAddress)->exists(),
                fn ($query) => $query->where('ip_address', $this->ipAddress),
                fn ($query) => $query->where([
                    'location_data->city' => Arr::get($ipLocationData, 'city'),
                    'location_data->region' => Arr::get($ipLocationData, 'region'),
                ])
            );
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
                sendNewIpNotification: $this->sendNewIpNotification,
            );
        }
    }
}