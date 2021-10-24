<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\SessionLength;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int
    {
        if (!empty($beacon['rt_sl'])) {
            return (int) $beacon['rt_sl'];
        }

        return 0;
    }
}
