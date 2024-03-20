<?php

namespace Zaengle\LaravelSecurityNotifications\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Login extends Model
{
    protected $table = 'logins';

    protected $guarded = [];

    protected $casts = [
        'first_login_at' => 'datetime',
        'last_login_at' => 'datetime',
        'location_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Authenticatable::class);
    }
}