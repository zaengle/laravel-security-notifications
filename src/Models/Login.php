<?php

namespace Zaengle\LaravelSecurityNotifications\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Zaengle\LaravelSecurityNotifications\Database\Factories\LoginFactory;

class Login extends Model
{
    use HasFactory;

    protected $table = 'logins';

    protected $guarded = [];

    protected $casts = [
        'first_login_at' => 'datetime',
        'last_login_at' => 'datetime',
        'location_data' => 'array',
    ];

    protected static function newFactory(): Factory
    {
        return LoginFactory::new();
    }

    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}