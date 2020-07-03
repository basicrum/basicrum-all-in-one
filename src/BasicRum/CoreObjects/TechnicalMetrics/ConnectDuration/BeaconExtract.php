<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\ConnectDuration;

use App\BasicRum\CoreObjects\BeaconExtractInterface;

class BeaconExtract implements BeaconExtractInterface
{
    public function extractValue(array $beacon): int
    {
        // Value from Navigation Timings plugin
        $value = (int) ($beacon['nt_con_end'] - $beacon['nt_con_st']);

        if ($value < 0) {
            $value = 0;
        }

        if ($value > 65535) {
            $value = 65535;
        }

        return $value;
    }
}
