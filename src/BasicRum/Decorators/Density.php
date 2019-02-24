<?php

declare(strict_types=1);

namespace App\BasicRum\Decorators;

class Density
    implements DecoratorInterface
{

    /** @var int */
    private $precision = 2;

    /**
     * @param array $buckets
     * @return array
     */
    public function decorate(array $buckets, array $samples) : array
    {
        $samplesCount = count($samples);
        $densityBuckets = [];

        foreach ($buckets as $key => $value) {
            if ($samplesCount === 0) {
                $densityBuckets[$key] = 0;
                continue;
            }

            $densityBuckets[$key] = number_format(($value / $samplesCount) * 100, $this->precision);
        }

        return $densityBuckets;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function isApplicable(array $options): bool
    {
        return true;
    }

}