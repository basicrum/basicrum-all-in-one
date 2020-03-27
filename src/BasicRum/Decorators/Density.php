<?php

declare(strict_types=1);

namespace App\BasicRum\Decorators;

class Density implements DecoratorInterface
{
    /** @var int */
    private $precision = 2;

    public function decorate(array $buckets, array $samples): array
    {
        $samplesCount = \count($samples);
        $densityBuckets = [];

        foreach ($buckets as $key => $value) {
            if (0 === $samplesCount) {
                $densityBuckets[$key] = 0;
                continue;
            }

            $densityBuckets[$key] = number_format(($value / $samplesCount) * 100, $this->precision);
        }

        return $densityBuckets;
    }

    public function isApplicable(array $options): bool
    {
        return true;
    }
}
