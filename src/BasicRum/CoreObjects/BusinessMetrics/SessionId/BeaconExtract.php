<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\BusinessMetrics\SessionId;

use App\BasicRum\CoreObjects\Interfaces\BeaconExtractStringInterface;

class BeaconExtract implements BeaconExtractStringInterface
{
    public function extractValue(array $beacon): string
    {
        if (!empty($beacon['rt_si'])) {
            return $beacon['rt_si'];
        }

        return '';
    }
}
