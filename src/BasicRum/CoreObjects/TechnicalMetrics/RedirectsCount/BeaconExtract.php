<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\RedirectsCount;

use App\BasicRum\CoreObjects\BeaconExtractInterface;

class BeaconExtract implements BeaconExtractInterface
{
    public function extractValue(array $beacon): int
    {
        $value = 0;

        if (!empty($beacon['nt_red_cnt'])) {
            $value = (int) $beacon['nt_red_cnt'];
        }

        return $value;
    }
}
