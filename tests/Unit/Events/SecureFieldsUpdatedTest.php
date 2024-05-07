<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;

it('emits event when secure fields are updated', function () {
    Event::fake([
        SecureFieldsUpdated::class,
    ]);

    $user = User::factory()->create();

    $user->update([
        'name' => 'New Name', // Not a secure field
        'email' => 'new@email.com',
        'username' => 'newusername',
        'password' => bcrypt('newpassword'),
    ]);

    Event::assertDispatched(SecureFieldsUpdated::class, function ($event) use ($user) {
        return $event->model->is($user)
            && Arr::has($event->fields, 'email')
            && Arr::has($event->fields, 'username')
            && Arr::has($event->fields, 'password')
            && ! Arr::has($event->fields, 'name');
    });
});

it('an event can be bypassed by setting the config value to false', function () {
    Event::fake([
        SecureFieldsUpdated::class,
    ]);

    $user = User::factory()->create();

    Config::set('security-notifications.send_notifications', false);

    $user->update([
        'name' => 'New Name', // Not a secure field
        'email' => 'new@email.com',
        'username' => 'newusername',
        'password' => bcrypt('newpassword'),
    ]);

    Event::assertNotDispatched(SecureFieldsUpdated::class);
});
