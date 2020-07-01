<?php

declare(strict_types=1);

namespace App\BasicRum\CoreObjects\TechnicalMetrics\LoadEventEnd;

use App\BasicRum\CoreObjects\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
{
    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }

    public function getFieldName(): string
    {
        return 'load_event_end';
    }
}
