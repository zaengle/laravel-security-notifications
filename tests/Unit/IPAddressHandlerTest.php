<?php

use Illuminate\Support\Facades\Bus;
use Zaengle\LaravelSecurityNotifications\Facades\IPAddress;
use Zaengle\LaravelSecurityNotifications\Jobs\ProcessNewIPAddress;
use Zaengle\LaravelSecurityNotifications\Models\Login;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;
use function Pest\Laravel\actingAs;

it('processes a new ip address', function () {
    Bus::fake();

    expect(Login::count())->toBe(0);

    $user = User::factory()->create();

    IPAddress::process([
        'ipAddress' => '127.0.0.1',
        'userId' => $user->getKey(),
        'userType' => $user->getMorphClass(),
    ]);

    Bus::assertDispatched(ProcessNewIPAddress::class, function ($job) use ($user) {
        return $job->ipAddress === '127.0.0.1'
            && $job->userId === $user->getKey()
            && $job->userType === $user->getMorphClass();
    });
});

it('processes an existing ip address', function () {
    $login = Login::factory()->create([
        'first_login_at' => now()->subDays(10),
        'last_login_at' => now()->subDays(5),
    ]);

    IPAddress::process([
        'ipAddress' => $login->ip_address,
        'userId' => $login->user_id,
        'userType' => $login->user_type,
    ]);

    expect($login->fresh()->last_login_at->toDateString())->toEqual(now()->toDateString());
});