<?php

namespace Zaengle\LaravelSecurityNotifications\Services;

abstract class DigestIPAddress
{
    abstract public function handle(): void;
}