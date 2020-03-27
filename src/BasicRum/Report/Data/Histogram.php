<?php

declare(strict_types=1);

namespace App\BasicRum\Report\Data;

class Histogram
{
    public function generate(array $mixBuckets): array
    {
        $allBuckets = [];
        $limit = 6000;

        foreach ($mixBuckets as $day => $dayMix) {
            if (!empty($dayMix['all_buckets'])) {
                $b = $dayMix['all_buckets'];

                foreach ($b as $bucketSize => $c) {
                    if ($bucketSize > $limit) {
                        unset($b[$bucketSize]);
                    }
                }

                $allBuckets[] = $b;
            }
        }

        $buckets = $this->mergeBuckets($allBuckets);

        return $buckets;
    }

    private function mergeBuckets(array $bucketsByDate): array
    {
        $sumArray = [];

        $bucketSize = 200;

        $maxArrayKeysCount = 0;

        foreach ($bucketsByDate as $date => $buckets) {
            $this->fillGapsWithZeroes($buckets, $bucketSize);

            $maxKey = array_key_last($buckets);
            $bucketCount = ($maxKey / $bucketSize) + 1;

            if ($bucketCount > $maxArrayKeysCount) {
                //Fill with zeroes

                $this->fillUpWithZeroes($sumArray, $maxArrayKeysCount, $bucketCount, $bucketSize);
                $maxArrayKeysCount = $bucketCount;
            }

            for ($i = 0; $i < $maxArrayKeysCount; ++$i) {
                $key = $i * $bucketSize;
                $sumArray[$key] = isset($buckets[$key]) ? $sumArray[$key] + $buckets[$key] : $sumArray[$key];
            }

            ksort($sumArray);
        }

        return $sumArray;
    }

    private function fillUpWithZeroes(array &$array, int $oldCount, int $newCount, int $keyDelta)
    {
        $fillUpWith = $newCount - $oldCount;

        for ($i = 0; $i < $fillUpWith; ++$i) {
            $key = ($oldCount + $i) * $keyDelta;
            $array[$key] = 0;
        }

        ksort($array);
    }

    private function fillGapsWithZeroes(array &$array, int $keyDelta)
    {
        $maxKey = array_key_last($array);
        $limit = $maxKey / $keyDelta;

        for ($i = 0; $i < $limit; ++$i) {
            $candidateKey = $i * $keyDelta;
            if (!isset($array[$candidateKey])) {
                $array[$candidateKey] = 0;
            }
        }

        ksort($array);
    }
}
