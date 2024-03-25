<?php

use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;

test('login belongs to a user', function () {
    $user = User::factory()->create();

    $login = $user->logins()->create([
        'ip_address' => '127.0.0.1',
        'first_login_at' => now(),
        'last_login_at' => now(),
        'location_data' => [
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ],
    ]);

    expect($user->logins->first()->is($login))->toBeTrue();
});