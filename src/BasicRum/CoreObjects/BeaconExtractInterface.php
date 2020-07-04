<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects;

interface BeaconExtractInterface
{
    public function extractValue(array $beacon);
}
