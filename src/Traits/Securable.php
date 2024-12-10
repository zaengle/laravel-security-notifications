<?php

namespace Zaengle\LaravelSecurityNotifications\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Zaengle\LaravelSecurityNotifications\Events\SecureFieldsUpdated;
use Zaengle\LaravelSecurityNotifications\Models\Login;

trait Securable
{
    public array $secureFields = [
        'email',
        'username',
        'password',
    ];

    public static function bootSecurable(): void
    {
        static::updated(function (Model $model) {
            self::handleUpdatedSecureFields($model);
        });
    }

    public static function handleUpdatedSecureFields(Model $model): void
    {
        if (config('security-notifications.send_notifications')) {
            $changedSecureFields = collect($model->getChanges())->only($model->getSecureFields());

            if ($changedSecureFields->count()) {
                $timezone = Arr::get($model->logins()->latest()->first()?->location_data, 'timezone', 'UTC');

                event(new SecureFieldsUpdated(
                    $model,
                    $changedSecureFields->toArray(),
                    $model->sendSecurityEmailsTo(),
                    $model->refresh()->updated_at->setTimezone($timezone),
                ));
            }
        }
    }

    public function sendSecurityEmailsTo(): string
    {
        return $this->getOriginal('email') ?? $this->email;
    }

    public function getSecureFields(): array
    {
        return $this->secureFields;
    }

    public function logins(): MorphMany
    {
        return $this->morphMany(Login::class, 'user');
    }
}
