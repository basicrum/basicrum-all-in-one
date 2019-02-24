<?php

declare(strict_types=1);

namespace App\BasicRum;

class Buckets
{

    /** @var int */
    private $_upperLimit = 5000;

    /** @var int */
    private $bucketSize;

    public function __construct(int $bucketSize)
    {
        $this->bucketSize = $bucketSize;
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
        $buckets = [0 => 0];

        //Initialize all buckets with ZEROES
        for($i = $this->bucketSize; $i <= $this->_upperLimit; $i += $this->bucketSize) {
            $buckets[$i] = 0;
        }

        // Fill buckets
        foreach ($samples as $sample) {
            $bucket = $this->bucketSize * (int) ($sample[$searchKey] / $this->bucketSize);

            if (300 >= $bucket) {
                continue;
            }

            if ($bucket >= 0 && $bucket <= $this->_upperLimit) {
                $buckets[$bucket]++;
            }
        }

        return $buckets;
    }

}