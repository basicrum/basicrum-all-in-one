<?php

declare(strict_types=1);

namespace App\BasicRum\Decorators;

class Densityzer
{

    /**
     * @param array $buckets
     * @param int $numberOfAllSamples
     * @param int $precision
     * @return array
     */
    public function fillDensity(array $buckets, int $numberOfAllSamples, int $precision)
    {
        $densityBuckets = [];

        foreach ($buckets as $key => $value) {
            if ($numberOfAllSamples === 0) {
                $densityBuckets[$key] = 0;
                continue;
            }

            $densityBuckets[$key] = number_format(($value / $numberOfAllSamples) * 100, $precision);
        }

        return $densityBuckets;
    }

}