<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\FirstContentfulPaint;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int
    {
        $value = 0;

        // Value from PaintTiming plugin
        if (!empty($beacon['pt_fcp'])) {
            $value = (int) $beacon['pt_fcp'];
        }

        if ($value < 0) {
            $value = 0;
        }

        if ($value > 65535) {
            $value = 65535;
        }

        return $value;
    }
}
