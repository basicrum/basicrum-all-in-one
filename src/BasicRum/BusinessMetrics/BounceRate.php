<?php

declare(strict_types=1);

namespace App\BasicRum\BusinessMetrics;

class BounceRate
    implements \App\BasicRum\Report\ComplexSelectableInterface
{

    public function getSecondarySelectDataFieldNames() : array
    {
        return [
            'pageViewsCount',
            'firstPageViewId',
            'guid'
        ];
    }

    public function getSecondarySelectEntityName() : string
    {
        return 'VisitsOverview';
    }

    public function getSecondaryKeyFieldName() : string
    {
        return 'firstPageViewId';
    }

    public function getPrimarySelectEntityName() : string
    {
        return 'NavigationTimings';
    }

    public function getPrimaryKeyFieldName() : string
    {
        return 'pageViewId';
    }

}