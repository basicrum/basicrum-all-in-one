<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\DownloadTime;

use App\BasicRum\Metrics\Interfaces\BeaconExtractIntInterface;


class BeaconExtract implements BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int|null
    {
        if (empty($beacon['nt_res_end']) || empty($beacon['nt_req_st'])) {
            return null;
        }

        $value = $beacon['nt_res_end'] - $beacon['nt_req_st'];

        if ($value < 0) {
            $value = 0;
        }

        if ($value > 65535) {
            $value = 65535;
        }

        return $value;
    }
}
