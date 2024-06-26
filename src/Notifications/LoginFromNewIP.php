<?php

namespace Zaengle\LaravelSecurityNotifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Zaengle\LaravelSecurityNotifications\Models\Login;

class LoginFromNewIP extends Notification
{
    public function __construct(public readonly Login $login)
    {
    }

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Login from new location')
            ->view('security-notifications::mail.login-from-new-ip', [
                'login' => $this->login,
            ]);
    }
}