<?php
declare(strict_types=1);

namespace App\BasicRum\Statistics;

class Median
{

    /**
     * @param array $buckets
     * @return int
     */
    function calculateMedian(array $buckets)
    {
        $sum = array_sum($buckets);
        $halfSum = $sum / 2;

        $scanSum = 0;

        foreach ($buckets as $bucket => $values)
        {
            if ($bucket <= 100) {
                continue;
            }

            $scanSum += $values;

            if ($halfSum <= $scanSum) {
                return $bucket;
            }
        }

        return 0;
    }
    
    
}