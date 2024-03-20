<?php

namespace Zaengle\LaravelSecurityNotifications\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;
use Zaengle\LaravelSecurityNotifications\Notifications\SecureFieldsUpdated as SecureFieldsUpdatedNotification;

class SendSecurityNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SecureFieldsUpdated $event): void
    {
        Notification::route('mail', $event->model->getSecurityNotificationsEmail())
            ->notify(new SecureFieldsUpdatedNotification($event->fields));
    }
}
