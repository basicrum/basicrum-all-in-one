<?php

declare(strict_types=1);

namespace App\BasicRum\Report\Data;

class BounceRate
{

    /**
     * @param array $buckets
     * @return array
     */
    public function generate(array $buckets) : array
    {
        $bounces  = [];

        foreach ($buckets as $bucketSize => $bucket) {
            $bounces[$bucketSize] = 0;
        }

        $bounceRatePercents = [];

        foreach ($buckets as $bucketSize => $bucket) {
            foreach ($bucket as $sample) {

                if ($sample['pageViewsCount'] == 1) {
                    $bounces[$bucketSize]++;
                }
            }
        }

        foreach ($buckets as $bucketSize => $bucket) {
            if (count($bucket) === 0) {
                $bounceRatePercents[$bucketSize] = 0;
                continue;
            }

            $bounceRatePercents[$bucketSize] = number_format(($bounces[$bucketSize] / count($bucket)) * 100, 2);
        }

        return $bounceRatePercents;
    }

}