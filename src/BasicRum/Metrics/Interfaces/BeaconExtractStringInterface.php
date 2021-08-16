<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Interfaces;

interface BeaconExtractStringInterface
{
    public function extractValue(array $beacon): string;
}
