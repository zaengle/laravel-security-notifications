<?php

namespace Zaengle\LaravelSecurityNotifications\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;

class SendSecurityNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SecureFieldsUpdated $event): void
    {
        $notificationClass = config('security-notifications.notifications.secure_fields');

        Notification::route('mail', $event->model->getSecurityNotificationsEmail())
            ->notify(new $notificationClass($event->fields));
    }
}
