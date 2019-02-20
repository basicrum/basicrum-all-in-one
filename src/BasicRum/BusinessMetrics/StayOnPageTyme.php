<?php

declare(strict_types=1);

namespace App\BasicRum\BusinessMetrics;

class StayOnPageTyme
    implements \App\BasicRum\Report\SelectableInterface
{

    public function getSelectDataFieldName(): string
    {
        return 'stayOnPageTime';
    }

    public function getSelectEntityName() : string
    {
        return 'NavigationTimings';
    }

}