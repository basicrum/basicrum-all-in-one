<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\SessionId;

use App\BasicRum\Metrics\Interfaces\BeaconExtractStringInterface;


class BeaconExtract implements BeaconExtractStringInterface
{
    public function extractValue(array $beacon): string
    {
        if (!empty($beacon['rt_si'])) {
            return $beacon['rt_si'];
        }

        return '';
    }
}
