<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Derived\Business\DeviceType;

use App\BasicRum\Metrics\Interfaces\DerivedDataExtractStringInterface;

class DerivedDataExtract implements DerivedDataExtractStringInterface
{
    public function extractValue(array $data): string
    {
        return !empty($data['device']['type']) ? $data['device']['type'] : '';
    }
}
