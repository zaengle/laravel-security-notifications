<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;
use Zaengle\LaravelSecurityNotifications\Models\Login;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\CustomUser;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;

it('emits event when secure fields are updated', function () {
    Event::fake([
        SecureFieldsUpdated::class,
    ]);

    $user = User::factory()->create();

    Login::factory()->for($user)->create();

    $originalEmail = $user->email;

    $user->update([
        'name' => 'New Name', // Not a secure field
        'email' => 'new@email.com',
        'username' => 'newusername',
        'password' => bcrypt('newpassword'),
    ]);

    Event::assertDispatched(SecureFieldsUpdated::class, function (SecureFieldsUpdated $event) use ($user, $originalEmail) {
        return $event->model->is($user)
            && $event->original_email === $originalEmail
            && Arr::has($event->fields, 'email')
            && Arr::has($event->fields, 'username')
            && Arr::has($event->fields, 'password')
            && ! Arr::has($event->fields, 'name');
    });
});

it('emits event when secure fields are updated and sends notification to configured email', function () {
    Event::fake([
        SecureFieldsUpdated::class,
    ]);

    $customUser = new CustomUser();
    $customUser->setRawAttributes(
        User::factory()->make([
            'alternate_email' => 'alternate_email@example.com',
        ])->getAttributes()
    );
    $customUser->save();

    $customUser->update([
        'name' => 'New Name', // Not a secure field
        'email' => 'new@email.com',
        'username' => 'newusername',
        'password' => bcrypt('newpassword'),
    ]);

    Event::assertDispatched(SecureFieldsUpdated::class, function (SecureFieldsUpdated $event) use ($customUser) {
        return $event->model->is($customUser)
            && $event->original_email === 'alternate_email@example.com'
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
