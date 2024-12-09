<?php

namespace Zaengle\LaravelSecurityNotifications\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Zaengle\LaravelSecurityNotifications\Models\Login;
use Zaengle\LaravelSecurityNotifications\Tests\Setup\Models\User;

class LoginFactory extends Factory
{
    protected $model = Login::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_type' => (new User)->getMorphClass(),
            'ip_address' => $ipAddress = $this->faker->ipv4,
            'first_login_at' => now(),
            'last_login_at' => now(),
            'location_data' => [
                'ipAddress' => $ipAddress,
                'city' => $this->faker->city,
                'region' => 'MN',
                'countryCode' => $this->faker->countryCode,
                'timezone' => 'America/Chicago',
            ],
        ];
    }
}