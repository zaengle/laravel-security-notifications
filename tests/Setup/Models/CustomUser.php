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

class CustomUser extends User
{
    public function sendSecurityEmailsTo(): string
    {
        return $this->getOriginal('alternate_email') ?? $this->alternate_email;
    }
}
