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
        new SecureFieldsUpdated($user, [
            'email' => 'new@email.com',
            'username' => 'newusername',
            'password' => bcrypt('newpassword'),
        ]),
    );

    Notification::assertSentOnDemand(SecureFieldsUpdatedNotification::class, function ($notification) {
        return Arr::has($notification->fields, 'email')
            && Arr::has($notification->fields, 'username')
            && Arr::has($notification->fields, 'password');
    });
});