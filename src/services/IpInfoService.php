<?php
declare(strict_types=1);

namespace Src\Services;

use ipinfo\ipinfo\IPinfo;

class IpInfoService
{
    public function __construct(
        private readonly IPinfo $ipinfo
    ) {
    }

    public function getLocation(string $ipAddress): string
    {
        return $this->ipinfo->getDetails($ipAddress)->all['abuse']['address'];
    }

    public function getFlag(string $ipAddress): string
    {
        return $this->ipinfo->getDetails($ipAddress)->countryFlag['emoji'];
    }
}
