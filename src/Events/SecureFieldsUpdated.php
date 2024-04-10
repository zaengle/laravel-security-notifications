<?php

namespace Zaengle\LaravelSecurityNotifications\Events;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class SecureFieldsUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Model $model,
        public readonly array $fields,
        public readonly string $original_email,
        public readonly Carbon $updated_at,
    ) {
    }
}
