<?php

namespace Zaengle\LaravelSecurityNotifications;

use Zaengle\LaravelSecurityNotifications\Exceptions\IPAddressDriverMissingException;

class IPAddressHandler
{
    public function process(array $options): void
    {
        $ipAddressDriver = config('security-notifications.ip_address_driver');

        if (!class_exists($ipAddressDriver)) {
            throw new IPAddressDriverMissingException("IP address driver [{$ipAddressDriver}] not found.");
        }

        (new $ipAddressDriver(...$options))->handle();
    }
}