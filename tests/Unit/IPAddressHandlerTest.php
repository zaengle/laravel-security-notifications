<?php

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Zaengle\LaravelSecurityNotifications\Exceptions\IPAddressDriverMissingException;
use Zaengle\LaravelSecurityNotifications\Facades\IPAddress;
use Zaengle\LaravelSecurityNotifications\Jobs\ProcessNewIPAddress;
use Zaengle\LaravelSecurityNotifications\Models\Login;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Services\CustomIPAddressDriver;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('processes a new ip address', function () {
    Bus::fake();

    expect(Login::count())->toBe(0);

    $user = User::factory()->create();

    Http::shouldReceive('retry')
        ->andReturnSelf();
    Http::shouldReceive('withQueryParameters')
        ->andReturnSelf();
    Http::shouldReceive('get')
        ->with('https://ip-api.com/json/127.0.0.1')
        ->once()
        ->andReturnSelf();
    Http::shouldReceive('json')
        ->once()
        ->andReturn([
            'query' => '127.0.0.1',
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ]);

    IPAddress::process([
        'ipAddress' => '127.0.0.1',
        'userId' => $user->getKey(),
        'userType' => $user->getMorphClass(),
    ]);

    Bus::assertDispatched(ProcessNewIPAddress::class, function ($job) use ($user) {
        return $job->ipLocationData === [
                'query' => '127.0.0.1',
                'city' => 'Minneapolis',
                'region' => 'MN',
                'countryCode' => 'US',
            ]
            && $job->userId === $user->getKey()
            && $job->userType === $user->getMorphClass();
    });
});

it('processes an existing ip address', function () {
    $login = Login::factory()->create([
        'first_login_at' => now()->subDays(10),
        'last_login_at' => now()->subDays(5),
    ]);

    Http::shouldReceive('retry')
        ->andReturnSelf();
    Http::shouldReceive('withQueryParameters')
        ->andReturnSelf();
    Http::shouldReceive('get')
        ->with('https://ip-api.com/json/'.$login->ip_address)
        ->once()
        ->andReturnSelf();
    Http::shouldReceive('json')
        ->once()
        ->andReturn([
            'query' => $login->ip_address,
            'city' => $login->location_data['city'],
            'region' => $login->location_data['region'],
            'countryCode' => $login->location_data['countryCode'],
        ]);

    IPAddress::process([
        'ipAddress' => $login->ip_address,
        'userId' => $login->user_id,
        'userType' => $login->user_type,
    ]);

    expect($login->fresh()->last_login_at->toDateString())->toEqual(now()->toDateString());
});

it('processes same location for user as existing login if configured', function () {
    Notification::fake();

    Config::set('security-notifications.allow_same_location_login', true);

    $login = Login::factory()->create([
        'ip_address' => '128.0.0.1',
        'first_login_at' => now()->subDays(10),
        'last_login_at' => now()->subDays(5),
        'location_data' => [
            'city' => 'Minneapolis',
            'region' => 'MN',
        ],
    ]);

    Http::shouldReceive('retry')
        ->andReturnSelf();
    Http::shouldReceive('withQueryParameters')
        ->andReturnSelf();
    Http::shouldReceive('get')
        ->with('https://ip-api.com/json/127.0.0.1')
        ->once()
        ->andReturnSelf();
    Http::shouldReceive('json')
        ->once()
        ->andReturn([
            'query' => '127.0.0.1',
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ]);

    IPAddress::process([
        'ipAddress' => '127.0.0.1',
        'userId' => $login->user_id,
        'userType' => $login->user_type,
    ]);

    expect($login->fresh()->last_login_at->toDateString())->toEqual(now()->toDateString())
        ->and($login->fresh()->ip_address)->toEqual('127.0.0.1');
});

it('processes same location for user as new login if not configured', function () {
    Bus::fake();

    Config::set('security-notifications.allow_same_location_login', false);

    $login = Login::factory()->create([
        'first_login_at' => now()->subDays(10),
        'last_login_at' => now()->subDays(5),
        'location_data' => [
            'city' => 'Minneapolis',
            'region' => 'MN',
        ],
    ]);

    Http::shouldReceive('retry')
        ->andReturnSelf();
    Http::shouldReceive('withQueryParameters')
        ->andReturnSelf();
    Http::shouldReceive('get')
        ->with('https://ip-api.com/json/127.0.0.1')
        ->once()
        ->andReturnSelf();
    Http::shouldReceive('json')
        ->once()
        ->andReturn([
            'query' => '127.0.0.1',
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ]);

    IPAddress::process([
        'ipAddress' => '127.0.0.1',
        'userId' => $login->user_id,
        'userType' => $login->user_type,
    ]);

    Bus::assertDispatched(ProcessNewIPAddress::class, function ($job) use ($login) {
        return $job->ipLocationData === [
                'query' => '127.0.0.1',
                'city' => 'Minneapolis',
                'region' => 'MN',
                'countryCode' => 'US',
            ]
            && $job->userId === $login->user->getKey()
            && $job->userType === $login->user->getMorphClass();
    });
});

it('updates correct login when multiple exist', function () {
    Config::set('security-notifications.allow_same_location_login', true);

    $user = User::factory()->create();

    $login = Login::factory()->for($user)->create([
        'ip_address' => '128.0.0.1',
        'first_login_at' => now()->subDays(10),
        'last_login_at' => now()->subDays(5),
        'location_data' => [
            'city' => 'Minneapolis',
            'region' => 'MN',
        ],
    ]);

    Login::factory()->for($user)->create([
        'ip_address' => '127.0.0.1',
        'first_login_at' => now()->subDays(10),
        'last_login_at' => now()->subDays(5),
        'location_data' => [
            'city' => 'Minneapolis',
            'region' => 'MN',
        ],
    ]);

    Http::shouldReceive('retry')
        ->andReturnSelf();
    Http::shouldReceive('withQueryParameters')
        ->andReturnSelf();
    Http::shouldReceive('get')
        ->with('https://ip-api.com/json/128.0.0.1')
        ->once()
        ->andReturnSelf();
    Http::shouldReceive('json')
        ->once()
        ->andReturn([
            'query' => '128.0.0.1',
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ]);

    IPAddress::process([
        'ipAddress' => '128.0.0.1',
        'userId' => $login->user_id,
        'userType' => $login->user_type,
    ]);

    expect($login->fresh()->last_login_at->toDateString())->toEqual(now()->toDateString())
        ->and($login->fresh()->ip_address)->toEqual('128.0.0.1');
});

it('processes an ip address with a custom driver', function () {
    Config::set('security-notifications.ip_address_driver', CustomIPAddressDriver::class);

    IPAddress::process([
        'ipAddress' => '127.0.0.1',
    ]);

    assertDatabaseHas('logins', [
        'ip_address' => '127.0.0.1',
        'user_id' => 1,
        'user_type' => User::class,
        'location_data' => json_encode(['custom' => 'driver']),
    ]);
});

it('throws an exception if the ip address driver is missing', function () {
    Config::set('security-notifications.ip_address_driver', null);

    IPAddress::process([
        'ipAddress' => '127.0.0.1',
    ]);
})->expectException(IPAddressDriverMissingException::class);

it('fails if no ip location data', function () {
    $login = Login::factory()->create();

    Http::shouldReceive('retry')
        ->andReturnSelf();
    Http::shouldReceive('get')
        ->with('http://ip-api.com/json/1234567890');
    Http::shouldReceive('json')
        ->never();

    IPAddress::process([
        'ipAddress' => '1234567890',
        'userId' => $login->user_id,
        'userType' => $login->user_type,
    ]);
})->expectException(Exception::class, 'Failed to get IP location data for: 1234567890');

it('uses pro endpoint if api key is set', function () {
    Bus::fake();

    Config::set('security-notifications.ip_api_key', 'test-key');

    $user = User::factory()->create();

    Http::shouldReceive('retry')
        ->andReturnSelf();
    Http::shouldReceive('withQueryParameters')
        ->with(['key' => 'test-key'])
        ->andReturnSelf();
    Http::shouldReceive('get')
        ->with('https://pro.ip-api.com/json/127.0.0.1')
        ->once()
        ->andReturnSelf();
    Http::shouldReceive('json')
        ->once()
        ->andReturn([
            'query' => '127.0.0.1',
            'city' => 'Minneapolis',
            'region' => 'MN',
            'countryCode' => 'US',
        ]);

    IPAddress::process([
        'ipAddress' => '127.0.0.1',
        'userId' => $user->getKey(),
        'userType' => $user->getMorphClass(),
    ]);
});