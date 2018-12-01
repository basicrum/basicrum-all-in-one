<?php

declare(strict_types=1);

namespace App\BasicRum\NavigationTiming;

class TimeToFirstByte implements MetricInterface
{
    /**
     * @return string
     */
    public function getMetricLabel()
    {
        return 'Time To First Byte';
    }

    /**
     * @return string
     */
    public function getMetricOffsetKey()
    {
        return 'first_byte';
    }

    /**
     * @return string
     */
    public function getInternalIdentifier()
    {
        return 'ttfb';
    }

}