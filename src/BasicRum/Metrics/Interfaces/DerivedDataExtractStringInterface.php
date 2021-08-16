<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Interfaces;

interface DerivedDataExtractStringInterface
{
    public function extractValue(array $data): string;
}
