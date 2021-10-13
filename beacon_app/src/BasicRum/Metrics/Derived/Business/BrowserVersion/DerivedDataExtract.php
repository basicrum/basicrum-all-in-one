<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Derived\Business\BrowserVersion;

use App\BasicRum\Metrics\Interfaces\DerivedDataExtractStringInterface;

class DerivedDataExtract implements DerivedDataExtractStringInterface
{
    public function extractValue(array $data): string
    {
        if (!empty($data['browser']) && !empty($data['browser']['version'])) {
            if (is_array($data['browser']['version'])) {
                return !empty($data['browser']['version']['value']) ? $data['browser']['version']['value'] : '';
            }
        }

        return !empty($data['browser']['version']) ? $data['browser']['version'] : '';
    }
}
