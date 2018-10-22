<?php

declare(strict_types=1);

namespace App\BasicRum\NavigationTiming;

interface MetricInterface
{

    public function getMetricLabel();

    public function getMetricOffsetKey();

    public function getInternalIdentifier();

}