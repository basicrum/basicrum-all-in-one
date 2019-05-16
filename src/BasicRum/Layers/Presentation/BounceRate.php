<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\Presentation;

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

        $bounceRate = [
            'x' => array_keys($bounceRatePercents),
            'y' => array_values($bounceRatePercents),
            'type' => 'line',
            'name' => 'Bounce Rate',
            'marker' => [
                'color' => 'rgb(255, 127, 14)'
            ],
//            'xaxis' => 'x2',
            'yaxis' => 'y2',
        ];

        return $bounceRate;
    }

}