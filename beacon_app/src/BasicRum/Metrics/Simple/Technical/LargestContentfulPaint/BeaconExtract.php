<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\LargestContentfulPaint;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int|null
    {
        $value = null;

        // Value from PaintTiming plugin
        if (!empty($beacon['pt_lcp'])) {
            $value = (int) $beacon['pt_lcp'];
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
