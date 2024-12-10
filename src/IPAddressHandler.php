<?php

namespace Zaengle\LaravelSecurityNotifications;

use Zaengle\LaravelSecurityNotifications\Exceptions\IPAddressDriverMissingException;
use Zaengle\LaravelSecurityNotifications\Objects\IPAddressHandlerObject;

class IPAddressHandler
{
    public function process(array $options): void
    {
        if (config('security-notifications.enabled')) {
            $ipAddressDriver = config('security-notifications.ip_address_driver');

            if (!class_exists($ipAddressDriver)) {
                throw new IPAddressDriverMissingException("IP address driver [{$ipAddressDriver}] not found.");
            }

            (new $ipAddressDriver(
                ...app(IPAddressHandlerObject::class, ['input' => $options])->input
            ))->handle();
        }
    }
}