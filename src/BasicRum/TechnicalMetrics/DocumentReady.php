<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class DocumentReady
    implements \App\BasicRum\Report\SelectableInterface
{

    public function getSelectDataFieldName(): string
    {
        return 'load_event_end';
    }

    public function getSelectTableName() : string
    {
        return 'navigation_timings';
    }

}