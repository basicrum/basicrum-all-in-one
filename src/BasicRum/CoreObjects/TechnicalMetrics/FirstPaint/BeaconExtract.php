<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\FirstPaint;

use App\BasicRum\CoreObjects\BeaconExtractInterface;

class BeaconExtract implements BeaconExtractInterface
{
    public function extractValue(array $beacon): int
    {
        // Value from Navigation Timings plugin
        $value = $beacon['nt_first_paint'] - $beacon['nt_nav_st'];

        // Value from PaintTiming plugin
        if (!empty($beacon['pt_fp'])) {
            $value = (int) $beacon['pt_fp'];
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
