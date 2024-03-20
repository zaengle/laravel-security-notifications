<?php

namespace Zaengle\LaravelSecurityNotifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Zaengle\LaravelSecurityNotifications\Models\Login;
use Zaengle\LaravelSecurityNotifications\Notifications\LoginFromNewIP;

class ProcessNewIPAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly string $ipAddress)
    {
    }

    public function handle()
    {
        $ipLocationData = Http::get('http://ip-api.com/json/'.$this->ipAddress)->json();

        /** @var Login $login */
        $login = Login::create([
            'ip_address' => $this->ipAddress,
            'user_id' => auth()->id(),
            'first_login_at' => now(),
            'last_login_at' => now(),
            'location_data' => $ipLocationData,
        ]);

        auth()->user()->notify(new LoginFromNewIP($login));
    }
}