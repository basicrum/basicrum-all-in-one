<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\CreatedAt;

use App\BasicRum\Metrics\Interfaces\BeaconExtractStringInterface;


class BeaconExtract implements BeaconExtractStringInterface
{
    public function extractValue(array $beacon): string
    {
        return !empty($beacon['created_at']) ? str_replace('2019-', '2021-', $beacon['created_at']) : '';
    }
}
