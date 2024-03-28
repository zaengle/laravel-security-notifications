<?php

namespace Zaengle\LaravelSecurityNotifications\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;
use Zaengle\LaravelSecurityNotifications\Models\Login;

trait Securable
{
    public static array $secureFields = [
        'email',
        'username',
        'password',
    ];

    public static function bootSecurable(): void
    {
        if (config('security-notifications.send_notifications')) {
            static::updated(function (Model $model) {
                $changedSecureFields = collect($model->getChanges())->only(self::$secureFields);

                if ($changedSecureFields->count()) {
                    event(new SecureFieldsUpdated(
                        $model,
                        $changedSecureFields->toArray(),
                        $model->getOriginal('email')),
                    );
                }
            });
        }
    }

    public function logins(): MorphMany
    {
        return $this->morphMany(Login::class, 'user');
    }
}
