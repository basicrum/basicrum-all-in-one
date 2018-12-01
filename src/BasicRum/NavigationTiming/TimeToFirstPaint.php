<?php

declare(strict_types=1);

namespace App\BasicRum\NavigationTiming;

class TimeToFirstPaint implements MetricInterface
{
    /**
     * @return string
     */
    public function getMetricLabel()
    {
        return 'Time To First Paint';
    }

    /**
     * @return string
     */
    public function getMetricOffsetKey()
    {
        return 'first_paint';
    }

    /**
     * @return string
     */
    public function getInternalIdentifier()
    {
        return 'ttfp';
    }

}