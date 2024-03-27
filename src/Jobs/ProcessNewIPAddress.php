<?php

namespace Zaengle\LaravelSecurityNotifications\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Zaengle\LaravelSecurityNotifications\Models\Login;

class ProcessNewIPAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?Model $user;

    public function __construct(
        public readonly string $ipAddress,
        public readonly int $userId,
        public readonly string $userType,
    )
    {
        $this->user = $this->userType::find($this->userId);
    }

    public function handle(): void
    {
        if (! $this->user) {
            throw new Exception('User does not exist.');
        }

        $ipLocationData = Http::get('http://ip-api.com/json/'.$this->ipAddress)->json();

        /** @var Login $login */
        $login = Login::create([
            'ip_address' => $this->ipAddress,
            'user_id' => $this->userId,
            'user_type' => $this->userType,
            'first_login_at' => now(),
            'last_login_at' => now(),
            'location_data' => $ipLocationData,
        ]);

        if (config('security-notifications.send_notifications')) {
            $notificationClass = config('security-notifications.notifications.secure_login');

            $this->user->notify(new $notificationClass($login));
        }
    }
}