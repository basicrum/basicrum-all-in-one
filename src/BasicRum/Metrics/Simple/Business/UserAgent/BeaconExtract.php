<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\UserAgent;

use App\BasicRum\Metrics\Interfaces\BeaconExtractStringInterface;


class BeaconExtract implements BeaconExtractStringInterface
{
    public function extractValue(array $beacon): string
    {
        return !empty($beacon['user_agent']) ? $beacon['user_agent'] : '';
    }
}
