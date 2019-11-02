<?php

declare(strict_types=1);

namespace App\BasicRum\Report\Data;

class BounceRate
{

    /**
     * @param array $mixBuckets
     * @return array
     */
    public function generate(array $mixBuckets) : array
    {
        $limit = 6000;

        $bounceRatePercents = [];

        $allBuckets = [];
        $bouncedBuckets = [];

        foreach ($mixBuckets as $day => $dayMix) {
            if (!empty($dayMix["bounced_buckets"])) {
                $bouncedBuckets[] = $dayMix["bounced_buckets"];
            }

            if (!empty($dayMix["all_buckets"])) {
                $allBuckets[] = $dayMix["all_buckets"];
            }
        }

        $mergedBucketsBuckets = $this->mergeBuckets($bouncedBuckets);
        $allBuckets = $this->mergeBuckets($allBuckets);


        foreach ($allBuckets as $bucketSize => $probes) {
            if ($bucketSize > $limit) {
                continue;
            }

            if ($probes === 0) {
                $bounceRatePercents[$bucketSize] = '0';
                continue;
            }

            $bounceRatePercents[$bucketSize] = number_format(($mergedBucketsBuckets[$bucketSize] / $probes) * 100, 0);
        }

        return $bounceRatePercents;
    }

    /**
     * @param array $bucketsByDate
     * @return array
     */
    private function mergeBuckets(array $bucketsByDate) : array
    {
        $sumArray = [];

        $bucketSize = 200;

        $maxArrayKeysCount = 0;

        foreach ($bucketsByDate as $date => $buckets)
        {
            $this->fillGapsWithZeroes($buckets, $bucketSize);

            $maxKey = array_key_last($buckets);
            $bucketCount = ($maxKey / $bucketSize) + 1;

            if ($bucketCount > $maxArrayKeysCount) {
                //Fill with zeroes

                $this->fillUpWithZeroes($sumArray, $maxArrayKeysCount, $bucketCount, $bucketSize);
                $maxArrayKeysCount = $bucketCount;
            }

            for ($i = 0; $i < $maxArrayKeysCount; $i++) {
                $key = $i * $bucketSize;
                $sumArray[$key] = isset($buckets[$key]) ? $sumArray[$key] + $buckets[$key] : $sumArray[$key];
            }

            ksort($sumArray);
        }

        return $sumArray;
    }

    /**
     * @param array $array
     * @param int $oldCount
     * @param int $newCount
     * @param int $keyDelta
     */
    private function fillUpWithZeroes(array &$array, int $oldCount, int $newCount, int $keyDelta)
    {
        $fillUpWith = $newCount - $oldCount;

        for ($i = 0; $i < $fillUpWith; $i++) {
            $key = ($oldCount + $i) * $keyDelta;
            $array[$key] = 0;
        }

        ksort($array);
    }

    /**
     * @param array $array
     * @param int $keyDelta
     */
    private function fillGapsWithZeroes(array &$array, int $keyDelta)
    {
        $maxKey = array_key_last($array);
        $limit = $maxKey / $keyDelta;

        for ($i = 0; $i < $limit; $i++) {
            $candidateKey = $i * $keyDelta;
            if (!isset($array[$candidateKey])) {
                $array[$candidateKey] = 0;
            }
        }

        ksort($array);
    }

}