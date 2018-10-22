<?php

declare(strict_types=1);

namespace App\BasicRum\NavigationTiming;

class MetricAggregator
{

    /**
     * @return array
     */
    public function getMetrics()
    {
        return [
            new TimeToFirstByte(),
            new TimeToFirstPaint()
        ];
    }

}