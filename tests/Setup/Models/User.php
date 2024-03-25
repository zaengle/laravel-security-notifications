<?php

namespace Zaengle\LaravelSecurityNotifications\Tests\Setup\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Factories\UserFactory;
use Zaengle\LaravelSecurityNotifications\Traits\Securable;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Securable, Authorizable, Authenticatable, HasFactory, Notifiable;

    protected $guarded = [];

    protected $table = 'users';

    public static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}