<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Business\RequestType;

use App\BasicRum\Metrics\Interfaces\BeaconExtractStringInterface;


class BeaconExtract implements BeaconExtractStringInterface
{

    const DEFAULT = 'page_visit';

    public function extractValue(array $beacon): string
    {
        return !empty($beacon['http_initiator']) ? $beacon['http_initiator'] : self::DEFAULT;
    }
}
