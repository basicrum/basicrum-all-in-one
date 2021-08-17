<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\RedirectDuration;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int
    {
        if (empty($beacon['nt_red_end']) || empty($beacon['nt_red_st'])) {
            return 0;
        }

        // Value from Navigation Timings plugin
        $value = (int) ($beacon['nt_red_end'] - $beacon['nt_red_st']);

        if ($value < 0) {
            $value = 0;
        }

        if ($value > 65535) {
            $value = 65535;
        }

        return $value;
    }
}
