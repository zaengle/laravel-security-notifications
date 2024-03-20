<?php

namespace Zaengle\LaravelSecurityNotifications\Traits;

use Illuminate\Database\Eloquent\Model;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;
use Zaengle\LaravelSecurityNotifications\Facades\IPAddress;

trait SendSecurityNotification
{
    public array $secureFields = [
        'email',
        'username',
        'password',
    ];

    public function getSecurityNotificationsEmail(): string
    {
        return $this->getOriginal('email');
    }

    public function bootSendSecurityNotifications(): void
    {
        if (env('SEND_SECURITY_NOTIFICATIONS', true)) {
            static::updated(function (Model $model) {
                $changedSecureFields = collect($model->getChanges())->only($this->secureFields);

                if ($changedSecureFields->count()) {
                    event(new SecureFieldsUpdated($model, $changedSecureFields->toArray()));
                }
            });
        }
    }
}
