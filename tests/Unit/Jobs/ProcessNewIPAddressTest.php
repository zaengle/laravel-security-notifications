<?php

use Illuminate\Http\Client\Response;
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

    Http::shouldReceive('get')
        ->with('http://ip-api.com/json/127.0.0.1')
        ->once()
        ->andReturnSelf();
    Http::shouldReceive('json')
        ->once()
        ->andReturn([
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ]);

    (new ProcessNewIPAddress(
        ipAddress: '127.0.0.1',
        userId: $user->getKey(),
        userType: $user->getMorphClass(),
    ))->handle();

    assertDatabaseHas('logins', [
        'ip_address' => '127.0.0.1',
        'user_id' => $user->getKey(),
        'user_type' => $user->getMorphClass(),
        'location_data' => json_encode([
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ]),
    ]);

    Notification::assertSentTo($user, LoginFromNewIP::class, function ($notification) {
        return $notification->login->ip_address === '127.0.0.1';
    });
});