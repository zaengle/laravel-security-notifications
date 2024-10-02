<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Zaengle\LaravelSecurityNotifications\Jobs\ProcessNewIPAddress;
use Zaengle\LaravelSecurityNotifications\Models\Login;
use Zaengle\LaravelSecurityNotifications\Notifications\LoginFromNewIP;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;
use function Pest\Laravel\assertDatabaseHas;

it('it processes a new ip address', function () {
    Notification::fake();

    $user = User::factory()->create();

    expect(Login::count())->toBe(0);

    (new ProcessNewIPAddress(
        ipLocationData: [
            'query' => '127.0.0.1',
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ],
        userId: $user->getKey(),
        userType: $user->getMorphClass(),
    ))->handle();

    assertDatabaseHas('logins', [
        'ip_address' => '127.0.0.1',
        'user_id' => $user->getKey(),
        'user_type' => $user->getMorphClass(),
        'location_data' => json_encode([
            'query' => '127.0.0.1',
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ]),
    ]);

    Notification::assertSentTo($user, LoginFromNewIP::class, function ($notification) {
        return $notification->login->ip_address === '127.0.0.1';
    });
});