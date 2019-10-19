<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class LastBlockingResource
    implements \App\BasicRum\Report\ComplexSelectableInterface

{

    public function getSecondarySelectDataFieldNames() : array
    {
        return [
            'page_view_id',
            'time'
        ];
    }

    public function getSecondarySelectTableName() : string
    {
        return 'last_blocking_resources';
    }

    public function getSecondaryKeyFieldName() : string
    {
        return 'page_view_id';
    }

    public function getPrimarySelectTableName() : string
    {
        return 'navigation_timings';
    }

    public function getPrimaryKeyFieldName() : string
    {
        return 'page_view_id';
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