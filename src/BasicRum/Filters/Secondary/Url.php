<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

class Url
    extends AbstractFilter
{

    public function getSecondaryEntityName() : string
    {
        return 'NavigationTimingsUrls';
    }

    public function getSecondaryKeyFieldName() : string
    {
        return 'id';
    }

    public function getSecondarySearchFieldName() : string
    {
        return 'url';
    }

    public function getPrimaryEntityName() : string
    {
        return 'NavigationTimings';
    }

    public function getPrimarySearchFieldName() : string
    {
        return 'urlId';
    }

}