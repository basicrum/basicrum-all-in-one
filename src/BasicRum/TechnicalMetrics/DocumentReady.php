<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class DocumentReady
    implements \App\BasicRum\Report\SelectableInterface
{

    public function getSelectDataFieldName(): string
    {
        return 'loadEventEnd';
    }

    public function getSelectEntityName() : string
    {
        return 'NavigationTimings';
    }

}