<?php

namespace Zaengle\LaravelSecurityNotifications\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class SecureFieldsUpdated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param Model $model
     * @param array<string> $fields
     */
    public function __construct(
        public readonly Model $model,
        public readonly array $fields,
    ) {
    }
}
