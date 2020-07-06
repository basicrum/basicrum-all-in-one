<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\BusinessMetrics\UserAgent;

use App\BasicRum\CoreObjects\Interfaces\BeaconExtractStringInterface;

class BeaconExtract implements BeaconExtractStringInterface
{
    public function extractValue(array $beacon): string
    {
        return !empty($beacon['user_agent']) ? $beacon['user_agent'] : '';
    }
}
