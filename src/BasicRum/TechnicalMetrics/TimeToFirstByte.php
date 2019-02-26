<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class TimeToFirstByte
    implements \App\BasicRum\Report\SelectableInterface
{

    public function getSelectDataFieldName(): string
    {
        return 'firstByte';
    }

    public function getSelectEntityName() : string
    {
        return 'NavigationTimings';
    }

}