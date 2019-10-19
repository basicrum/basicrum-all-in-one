<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class TimeToFirstByte
    implements \App\BasicRum\Report\SelectableInterface
{

    public function getSelectDataFieldName(): string
    {
        return 'first_byte';
    }

    public function getSelectTableName() : string
    {
        return 'navigation_timings';
    }

}