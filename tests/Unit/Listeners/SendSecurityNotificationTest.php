<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;
use Zaengle\LaravelSecurityNotifications\Listeners\SendSecurityNotification;
use Zaengle\LaravelSecurityNotifications\Notifications\SecureFieldsUpdated as SecureFieldsUpdatedNotification;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;

it('sends the security notification', function () {
    Notification::fake();

    $user = User::factory()->create();

    (new SendSecurityNotification)->handle(
        new SecureFieldsUpdated(
            $user,
            [
                'email' => 'new@email.com',
                'username' => 'newusername',
                'password' => bcrypt('newpassword'),
            ],
            $user->email,
            $user->fresh()->updated_at,
        ),
    );

    Notification::assertSentOnDemand(SecureFieldsUpdatedNotification::class, function ($notification) {
        return Arr::has($notification->fields, 'email')
            && Arr::has($notification->fields, 'username')
            && Arr::has($notification->fields, 'password');
    });
});

it('does not send the notification if the email is an empty string', function () {
    Notification::fake();

    $user = User::factory()->create();

    $event = (new SecureFieldsUpdated(
        model: $user,
        fields: [],
        original_email: '',
        updated_at: now(),
    ));

    (new SendSecurityNotification)->handle($event);

    Notification::assertNothingSent();
});