<?php

return [
    // Enable or disable the security package
    'enabled' => true,

    // Configure whether security notifications should be sent
    'send_notifications' => true,

    // Configure the table name for the logins model
    'logins_table' => 'logins',

    // Configure the notification classes to use
    'notifications' => [
        'secure_login' => \Zaengle\LaravelSecurityNotifications\Notifications\LoginFromNewIP::class,
        'secure_fields' => \Zaengle\LaravelSecurityNotifications\Notifications\SecureFieldsUpdated::class,
    ],

    // Configure the IP address driver
    'ip_address_driver' => \Zaengle\LaravelSecurityNotifications\Services\IPAddressDriver::class,

    // Configure whether to allow the location for different IP addresses for a user.
    // This is useful when using a network which switches IP addresses frequently. Not recommended for most use cases.
    // Will check if given user has logged in from the given city, and region.
    'allow_same_location_login' => false,
];