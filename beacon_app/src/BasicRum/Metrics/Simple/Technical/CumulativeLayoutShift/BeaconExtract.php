<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\CumulativeLayoutShift;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;

class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): float|null
    {
        $value = null;

        if (!empty($beacon['c_cls'])) {
            $value = (float) $beacon['c_cls'];
            $value = round($value, 4);
        }

        return $value;
    }
}
