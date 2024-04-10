<?php

namespace Zaengle\LaravelSecurityNotifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecureFieldsUpdated extends Notification
{
    /**
     * @param array<string> $fields
     */
    public function __construct(
        public readonly array $fields,
        public readonly Carbon $updated_at,
    )
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
            ->subject('Your Account Details Have Changed')
            ->view('security-notifications::mail.security-alert', [
                'fields' => $this->fields,
                'updated_at' => $this->updated_at,
            ]);
    }
}
