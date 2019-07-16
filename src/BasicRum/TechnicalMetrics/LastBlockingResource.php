<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class LastBlockingResource
    implements \App\BasicRum\Report\ComplexSelectableInterface

{

    public function getSecondarySelectDataFieldNames() : array
    {
        return [
            'pageViewId',
            'time'
        ];
    }

    public function getSecondarySelectEntityName() : string
    {
        return 'LastBlockingResources';
    }

    public function getSecondaryKeyFieldName() : string
    {
        return 'pageViewId';
    }

    public function getPrimarySelectEntityName() : string
    {
        return 'NavigationTimings';
    }

    public function getPrimaryKeyFieldName() : string
    {
        return 'pageViewId';
    }

    /**
     * Still not sure what to do with this because we need this method for technical metrics but
     * actually this method is required by App\BasicRum\Report\SelectableInterface
     *
     * Huge refactoring is coming :) But not today, not tomorrow ...
     *
     * @return string
     */
    public function getSelectDataFieldName() : string
    {
        return 'time';
    }

}