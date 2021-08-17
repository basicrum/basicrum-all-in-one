<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Derived\Business\BrowserName;

use App\BasicRum\Metrics\Interfaces\DerivedDataExtractStringInterface;

class DerivedDataExtract implements DerivedDataExtractStringInterface
{
    public function extractValue(array $data): string
    {
        return !empty($data['browser']['name']) ? $data['browser']['name'] : '';
    }
}
