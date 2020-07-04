<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\SessionId;

use App\BasicRum\CoreObjects\BeaconExtractInterface;

class BeaconExtract implements BeaconExtractInterface
{
    public function extractValue(array $beacon): string
    {
        if (!empty($beacon['rt_si'])) {
            return $beacon['rt_si'];
        }

        return '';
    }
}
