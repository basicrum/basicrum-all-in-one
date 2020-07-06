<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\Interfaces;

interface BeaconExtractIntInterface
{
    public function extractValue(array $beacon): int;
}
