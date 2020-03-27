<?php

declare(strict_types=1);

namespace App\BasicRum\BusinessMetrics;

class StayOnPageTyme implements \App\BasicRum\Report\SelectableInterface
{
    public function getSelectDataFieldName(): string
    {
        return 'stay_on_page_time';
    }

    public function getSelectTableName(): string
    {
        return 'navigation_timings';
    }
}
