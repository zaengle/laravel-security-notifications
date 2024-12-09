<?php

namespace Zaengle\LaravelSecurityNotifications\Services;

use ArrayObject;
use InvalidArgumentException;

class IPLocationData extends ArrayObject
{
    private array $requiredKeys = [
        'ipAddress',
        'countryCode',
        'region',
        'city',
        'timezone',
    ];

    private array $allowedKeys = [
        'country',
        'regionName',
        'status',
        'continent',
        'continentCode',
        'district',
        'zip',
        'lat',
        'lon',
        'offset',
        'currency',
        'isp',
        'org',
        'as',
        'asname',
        'mobile',
        'proxy',
        'hosting',
    ];

    public function __construct(public array $input)
    {
        $this->validate($input);

        parent::__construct($input);
    }

    private function validate(array $input): void
    {
        foreach ($this->requiredKeys as $key) {
            if (! array_key_exists($key, $input)) {
                throw new InvalidArgumentException("Missing required key: $key");
            }
        }

        foreach (array_keys($input) as $key) {
            if (! in_array($key, array_merge($this->requiredKeys, $this->allowedKeys))) {
                throw new InvalidArgumentException("Invalid key: $key");
            }
        }
    }
}