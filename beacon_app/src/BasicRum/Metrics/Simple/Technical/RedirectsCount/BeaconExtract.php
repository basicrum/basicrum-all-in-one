<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\RedirectsCount;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
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
