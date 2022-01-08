<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\LoadEventEnd;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int|null
    {
        if (empty($beacon['nt_load_end']) || empty($beacon['nt_nav_st'])) {
            return null;
        }

        $value = $beacon['nt_load_end'] - $beacon['nt_nav_st'];

        if ($value < 0) {
            $value = 0;
        }

        if ($value > 65535) {
            $value = 65535;
        }

        return $value;
    }
}