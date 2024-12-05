<?php

namespace Zaengle\LaravelSecurityNotifications\Services;

class IPLocationData
{
    public function __construct(
        public readonly string $ipAddress,
        public readonly string $country,
        public readonly string $countryCode,
        public readonly string $region,
        public readonly string $regionName,
        public readonly string $city,
        public readonly string $timezone,
        public readonly ?string $status = null,
        public readonly ?string $continent = null,
        public readonly ?string $continentCode = null,
        public readonly ?string $district = null,
        public readonly ?string $zip = null,
        public readonly ?float $lat = null,
        public readonly ?float $lon = null,
        public readonly ?string $offset = null,
        public readonly ?string $currency = null,
        public readonly ?string $isp = null,
        public readonly ?string $org = null,
        public readonly ?string $as = null,
        public readonly ?string $asname = null,
        public readonly ?bool $mobile = null,
        public readonly ?bool $proxy = null,
        public readonly ?bool $hosting = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'ipAddress' => $this->ipAddress,
            'status' => $this->status,
            'continent' => $this->continent,
            'continentCode' => $this->continentCode,
            'country' => $this->country,
            'countryCode' => $this->countryCode,
            'region' => $this->region,
            'regionName' => $this->regionName,
            'city' => $this->city,
            'district' => $this->district,
            'zip' => $this->zip,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'timezone' => $this->timezone,
            'offset' => $this->offset,
            'currency' => $this->currency,
            'isp' => $this->isp,
            'org' => $this->org,
            'as' => $this->as,
            'asname' => $this->asname,
            'mobile' => $this->mobile,
            'proxy' => $this->proxy,
            'hosting' => $this->hosting,
        ];
    }
}