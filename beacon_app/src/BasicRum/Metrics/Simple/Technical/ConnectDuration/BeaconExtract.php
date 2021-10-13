<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\ConnectDuration;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int|null
    {
        if (empty($beacon['nt_con_end']) || empty($beacon['nt_con_st'])) {
            return null;
        }

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
