<?php

namespace Zaengle\LaravelSecurityNotifications\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecureFieldsUpdated extends Notification
{
    /**
     * @param array<string> $fields
     */
    public function __construct(public readonly array $fields)
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
        return (new MailMessage())
            ->view('security-notifications::mail.security-alert', [
                'fields' => $this->fields,
            ]);
    }
}
