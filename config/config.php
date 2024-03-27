<?php

return [
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
    'ip_address_driver' => \Zaengle\LaravelSecurityNotifications\Services\IPAddress::class,
];