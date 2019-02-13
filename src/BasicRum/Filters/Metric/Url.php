<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Metric;

class Url
    extends AbstractFilter
{

    public function getDataField() : string
    {
        return 'url';
    }

    public function getEntity() : string
    {
        return 'NavigationTimingsUrls';
    }

    public function getRelatedEntity() : string
    {
        return 'NavigationTimings';
    }

    public function getKeyField() : string
    {
        return 'id';
    }

    public function getRelatedKeyField() : string
    {
        return 'url_id';
    }

}