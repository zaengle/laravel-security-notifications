<?php

namespace Zaengle\LaravelSecurityNotifications\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Zaengle\LaravelSecurityNotifications\Models\Login;
use Zaengle\LaravelSecurityNotifications\Objects\IPLocationData;

class ProcessNewIPAddress implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?Model $user;

    public function __construct(
        public readonly IPLocationData $ipLocationData,
        public readonly int $userId,
        public readonly string $userType,
        public readonly bool $sendNewIpNotification = true,
    )
    {
        $this->user = $this->userType::find($this->userId);
    }

    public function handle(): void
    {
        if (! $this->user) {
            throw new Exception('User does not exist.');
        }

        $localizedTime = Carbon::now($this->ipLocationData['timezone']);

        /** @var Login $login */
        $login = Login::create([
            'ip_address' => $this->ipLocationData['ipAddress'],
            'user_id' => $this->userId,
            'user_type' => $this->userType,
            'first_login_at' => $localizedTime,
            'last_login_at' => $localizedTime,
            'location_data' => $this->ipLocationData->input,
        ]);

        if (config('security-notifications.send_notifications') && $this->sendNewIpNotification) {
            $notificationClass = config('security-notifications.notifications.secure_login');

            $this->user->notify(new $notificationClass($login));
        }
    }

    public function uniqueId(): string
    {
        return $this->userId.'-'.$this->ipLocationData['ipAddress'];
    }
}
