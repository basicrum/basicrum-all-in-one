<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\TimeToFirstByte;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int|null
    {
        if (empty($beacon['nt_res_st']) || empty($beacon['nt_nav_st'])) {
            return null;
        }

        $value = $beacon['nt_res_st'] - $beacon['nt_nav_st'];

        if ($value < 0) {
            $value = 0;
        }

        if ($value > 65535) {
            $value = 65535;
        }

        return $value;
    }
}
