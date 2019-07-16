<?php

declare(strict_types=1);

namespace App\BasicRum;

class Buckets
{

    /** @var int */
    private $_upperLimit = 15000;

    /** @var int */
    private $bucketSize;

    public function __construct(int $bucketSize, int $upperLimit)
    {
        $this->bucketSize  = $bucketSize;
        $this->_upperLimit = $upperLimit;
    }

    /**
     * @param array $periodSamples
     * @param string $searchKey
     * @return array
     */
    public function bucketizePeriod(array $periodSamples, string $searchKey) : array
    {
        $buckets = [];

        foreach ($periodSamples as $samples) {
            $subBuckets = $this->bucketize($samples, $searchKey);

            foreach ($subBuckets as $bucketNumber => $subBucket) {
                $buckets[$bucketNumber] = isset($buckets[$bucketNumber]) ? array_merge($buckets[$bucketNumber], $subBucket) : $subBucket;
            }
        }

        return $buckets;
    }

    /**
     * @param array $samples
     * @param string $searchKey
     *
     * @return array
     */
    public function bucketize(array $samples, string $searchKey)  : array
    {
        // Initialize the ZERO bucket
        $buckets = [0 => []];

        //Initialize all buckets with ZEROES
        for($i = $this->bucketSize; $i <= $this->_upperLimit; $i += $this->bucketSize) {
            $buckets[$i] = [];
        }

        // Fill buckets
        foreach ($samples as $sample) {
            if (!isset($sample[$searchKey])) {
                continue;
            }
            if (isset($sample['firstPaint'])) {
                if (100 >= $sample['firstPaint']) {
                    continue;
                }
            }

            if (isset($sample['firstByte'])) {
                if (100 >= $sample['firstByte']) {
                    continue;
                }
            }

            $bucket = $this->bucketSize * (int) ($sample[$searchKey] / $this->bucketSize);

            if (100 >= $bucket) {
                continue;
            }

            if ($bucket >= 0 && $bucket <= $this->_upperLimit) {
                $buckets[$bucket][] = $sample;
            }
        }

        return $buckets;
    }

}