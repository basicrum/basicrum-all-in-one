<?php

declare(strict_types=1);

namespace App\BasicRum;

class Bucketizer
{

    /** @var int */
    private $_upperLimit = 10000;

    /**
     * @param array $samples
     * @param int $bucketSize
     * @return array
     */
    public function bucketize(array $samples, int $bucketSize)
    {
        // Initialize the ZERO bucket
        $buckets = [0 => 0];

        //Initialize all buckets with ZEROES
        for($i = $bucketSize; $i <= $this->_upperLimit; $i += $bucketSize) {
            $buckets[$i] = 0;
        }

        // Fill buckets
        foreach ($samples as $sample) {
            $bucket = $bucketSize * (int) ($sample / $bucketSize);
            if ($bucket >= 0 && $bucket <= $this->_upperLimit) {
                $buckets[$bucket]++;
            }
        }

        return $buckets;
    }

}