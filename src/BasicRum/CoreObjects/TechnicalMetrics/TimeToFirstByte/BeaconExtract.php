<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\TimeToFirstByte;

use App\BasicRum\CoreObjects\Interfaces\BeaconExtractIntInterface;

class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int
    {
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
