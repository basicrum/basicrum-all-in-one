<?php

declare(strict_types=1);

namespace App\BasicRum;

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
            $densityBuckets[$key] = number_format(($value / $numberOfAllSamples) * 100, $precision);
        }

        return $densityBuckets;
    }

}