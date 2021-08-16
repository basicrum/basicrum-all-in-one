<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\DnsDuration;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int|null
    {
        if (empty($beacon['nt_dns_end']) || empty($beacon['nt_dns_st'])) {
            return null;
        }

        // Value from Navigation Timings plugin
        $value = (int) ($beacon['nt_dns_end'] - $beacon['nt_dns_st']);

        if ($value < 0) {
            $value = 0;
        }

        if ($value > 65535) {
            $value = 65535;
        }

        return $value;
    }
}
