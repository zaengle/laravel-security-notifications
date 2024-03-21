<?php

namespace Zaengle\LaravelSecurityNotifications\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;
use Zaengle\LaravelSecurityNotifications\Models\Login;

trait HasLogins
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

    public static function bootHasLogins(): void
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

    public function logins(): MorphMany
    {
        return $this->morphMany(Login::class, 'user');
    }
}
