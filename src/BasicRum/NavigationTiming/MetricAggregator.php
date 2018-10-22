<?php

declare(strict_types=1);

namespace App\BasicRum\NavigationTiming;

class MetricAggregator
{

    /** @var array */
    private $metrics = [];

    public function __construct()
    {
        $this->metrics = [
            new TimeToFirstByte(),
            new TimeToFirstPaint()
        ];
    }

    /**
     * @return array
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

}