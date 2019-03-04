<?php

declare(strict_types=1);

namespace App\BasicRum\BusinessMetrics;

class PageViewsCount
    implements \App\BasicRum\Report\CountableInterface
{

    /**
     * @return string
     */
    public function getSelectDataFieldName() : string
    {
        return 'pageViewId';
    }

    /**
     * @return string
     */
    public function getSelectEntityName() : string
    {
        return 'NavigationTimings';
    }

}