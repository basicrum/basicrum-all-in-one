<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\Url;

use App\BasicRum\Metrics\Interfaces\BeaconExtractStringInterface;


class BeaconExtract implements BeaconExtractStringInterface
{
    public function extractValue(array $beacon): string
    {
        return !empty($beacon['u']) ? $beacon['u'] : '';
    }
}
