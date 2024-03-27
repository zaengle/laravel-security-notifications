<?php

namespace Zaengle\LaravelSecurityNotifications;

readonly class IPAddressHandler
{
    public function process(array $options): void
    {
        $ipAddressDriver = config('security-notifications.ip_address_driver');

        (new $ipAddressDriver(...$options))->handle();
    }
}