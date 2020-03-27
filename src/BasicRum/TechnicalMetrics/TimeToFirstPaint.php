<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class TimeToFirstPaint implements \App\BasicRum\Report\SelectableInterface
{
    public function getSelectDataFieldName(): string
    {
        return 'first_paint';
    }

    public function getSelectTableName(): string
    {
        return 'navigation_timings';
    }
}
