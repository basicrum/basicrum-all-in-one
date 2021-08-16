<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Interfaces;

interface BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int|null;
}
