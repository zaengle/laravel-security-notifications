<?php

namespace Zaengle\LaravelSecurityNotifications\Tests\Setup\Factories;

use Illuminate\Support\Str;
use Orchestra\Testbench\Factories\UserFactory as TestBenchUserFactory;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;

class UserFactory extends TestBenchUserFactory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }
}